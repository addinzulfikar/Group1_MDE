<?php

namespace App\Repositories\Eloquent;

use App\Models\Fleet;
use App\Models\FleetLog;
use App\Models\Hub;
use App\Models\Package;
use App\Models\Warehouse;
use App\Repositories\Contracts\FleetRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class FleetRepository implements FleetRepositoryInterface
{
    private const FLEET_PAGINATION_SIZE = 15;

    public function getAllFleets($search = null)
    {
        $query = Fleet::with('currentHub')->latest();

        $this->applyFleetSearch($query, $search);

        return $query->paginate(self::FLEET_PAGINATION_SIZE)->withQueryString();
    }

    public function getFleetById($id)
    {
        return Fleet::with(['currentHub', 'logs.originHub', 'logs.destinationHub'])->findOrFail($id);
    }

    public function getMissingPackageIds(array $packageIds): array
    {
        $requestedIds = collect($packageIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values();

        if ($requestedIds->isEmpty()) {
            return [];
        }

        $existingIds = Package::query()
            ->whereIn('id', $requestedIds)
            ->pluck('id');

        return $requestedIds
            ->diff($existingIds)
            ->values()
            ->all();
    }

    public function calculateTransitDuration($fleetId)
    {
        $fleet = Fleet::with('currentHub')->findOrFail($fleetId);

        $allLogs = $this->getFleetLogs((int) $fleetId);
        $completedLogs = $this->filterCompletedTransitLogs($allLogs);
        $history = $this->mapTransitHistory($completedLogs, (string) $fleet->plate_number);
        $summary = $this->buildTransitSummary($allLogs, $history, $completedLogs);

        return [
            // Legacy keys kept for existing frontend compatibility.
            'fleet_id' => $fleet->id,
            'average_duration_hours' => $summary['average_duration_hours'],
            'history' => $history,

            'fleet' => $this->formatFleetDetails($fleet),
            'summary' => $summary,
            'route_stats' => $this->buildRouteStats($history),
        ];
    }

    public function calculateLoadPlan(
        int $fleetId,
        array $packageIds = [],
        array $packageQuantities = [],
        string $strategy = 'maximize_count',
        bool $includeBreakdown = false
    ): array {
        $fleet = Fleet::findOrFail($fleetId);
        $capacityKg = (float) $fleet->capacity;

        [$quantityByPackageId, $orderedPackageIds] = $this->buildRequestedPackageMap($packageIds, $packageQuantities);

        $requestedPackageIds = collect(array_keys($quantityByPackageId));
        [$packagesById, $missingPackageIds] = $this->loadPackagesById($requestedPackageIds);

        $volumetricDivisor = $this->getVolumetricDivisorByFleetType((string) $fleet->type);
        $calculatedPackages = $this->buildCalculatedPackages(
            $requestedPackageIds,
            $packagesById,
            $quantityByPackageId,
            $volumetricDivisor
        );

        $weightSummary = $this->summarizePackageWeights($calculatedPackages, $capacityKg);
        $loadingSequence = $this->buildLoadingSequence($calculatedPackages, $orderedPackageIds, $strategy);
        $loadingSimulation = $this->simulateCapacityLoading($loadingSequence, $capacityKg);

        $response = [
            'fleet' => [
                'id' => $fleet->id,
                'plate_number' => $fleet->plate_number,
                'type' => $fleet->type,
                'capacity_kg' => $capacityKg,
            ],
            'formula' => [
                'description' => 'chargeable_weight_kg = max(actual_weight_kg, volume_cm3 / divisor)',
                'volumetric_divisor' => $volumetricDivisor,
                'strategy' => $strategy,
            ],
            'summary' => [
                'selected_package_count' => $weightSummary['selected_package_count'],
                'selected_unique_package_count' => $calculatedPackages->count(),
                'missing_package_ids' => $missingPackageIds,
                'total_actual_weight_kg' => $weightSummary['total_actual_weight_kg'],
                'total_volumetric_weight_kg' => $weightSummary['total_volumetric_weight_kg'],
                'total_chargeable_weight_kg' => $weightSummary['total_chargeable_weight_kg'],
                'fleet_capacity_kg' => $capacityKg,
                'utilization_percentage_if_all_loaded' => $weightSummary['utilization_percentage_if_all_loaded'],
                'can_fit_all_packages' => $weightSummary['can_fit_all_packages'],
                'over_capacity_kg' => $weightSummary['over_capacity_kg'],
                'average_chargeable_weight_per_package_kg' => $weightSummary['average_chargeable_weight_per_package_kg'],
                'estimated_max_packages_by_average' => $weightSummary['estimated_max_packages_by_average'],
            ],
            'loading_simulation' => [
                'loaded_package_count' => $loadingSimulation['loaded_package_count'],
                'overflow_package_count' => $loadingSimulation['overflow_package_count'],
                'used_capacity_kg' => $loadingSimulation['used_capacity_kg'],
                'remaining_capacity_kg' => $loadingSimulation['remaining_capacity_kg'],
                'loaded_package_ids' => collect($loadingSimulation['loaded_packages'])->pluck('id')->values()->all(),
                'overflow_package_ids' => collect($loadingSimulation['overflow_packages'])->pluck('id')->values()->all(),
            ],
        ];

        if ($includeBreakdown) {
            $response['package_breakdown'] = [
                'all_requested_packages' => $calculatedPackages,
                'loaded_packages' => $loadingSimulation['loaded_packages'],
                'overflow_packages' => $loadingSimulation['overflow_packages'],
            ];
        }

        return $response;
    }

    public function storeFleet(array $data)
    {
        return Fleet::create($data);
    }

    public function updateFleetStatus($id, $status)
    {
        $fleet = Fleet::findOrFail($id);
        $oldStatus = (string) $fleet->status;
        $newStatus = (string) $status;

        if ($oldStatus === $newStatus) {
            return $fleet;
        }

        $fleet->status = $newStatus;
        $fleet->save();

        $this->syncStatusLoadTransition($fleet, $oldStatus, $newStatus);

        return $fleet;
    }

    public function relocateFleet($id, $newHubId)
    {
        $fleet = Fleet::findOrFail($id);

        $oldHubId = $fleet->current_hub_id;
        $destinationHubId = (int) $newHubId;

        if ((int) $oldHubId === $destinationHubId) {
            return $fleet;
        }

        // Logic error fix: idle/maintenance fleets are EMPTY. 
        // Relocating them should NOT transfer any "ghost load" between warehouses.
        // We only update the hub location.
        $fleet->current_hub_id = $destinationHubId;
        
        if ($fleet->status === 'in_transit') {
            $fleet->status = 'idle';
        }
        $fleet->save();

        $this->logFleetRelocation($fleet, (int) ($oldHubId ?: $destinationHubId), $destinationHubId);

        return $fleet;
    }

    private function applyFleetSearch(Builder $query, ?string $search): void
    {
        if (!$search) {
            return;
        }

        $query->where(function (Builder $builder) use ($search) {
            $builder->where('plate_number', 'like', "%{$search}%")
                ->orWhere('type', 'like', "%{$search}%");
        });
    }

    private function getFleetLogs(int $fleetId): Collection
    {
        return FleetLog::with(['originHub:id,name', 'destinationHub:id,name'])
            ->where('fleet_id', $fleetId)
            ->orderByDesc('departed_at')
            ->orderByDesc('id')
            ->get();
    }

    private function filterCompletedTransitLogs(Collection $logs): Collection
    {
        return $logs
            ->filter(fn (FleetLog $log): bool => !is_null($log->departed_at) && !is_null($log->arrived_at))
            ->values();
    }

    private function mapTransitHistory(Collection $completedLogs, string $plateNumber): Collection
    {
        return $completedLogs->map(function (FleetLog $log) use ($plateNumber) {
            $departed = Carbon::parse($log->departed_at);
            $arrived = Carbon::parse($log->arrived_at);

            return [
                'log_id' => $log->id,
                'plate_number' => $plateNumber,
                'status' => $log->status,
                'origin_hub_id' => $log->origin_hub_id,
                'origin_hub_name' => $log->originHub?->name,
                'destination_hub_id' => $log->destination_hub_id,
                'destination_hub_name' => $log->destinationHub?->name,
                'departed_at' => $log->departed_at,
                'arrived_at' => $log->arrived_at,
                'duration_hours' => round($departed->diffInMinutes($arrived) / 60, 2),
            ];
        })->values();
    }

    private function buildRouteStats(Collection $history): Collection
    {
        return $history
            ->groupBy(fn (array $item): string => $item['origin_hub_id'] . '-' . $item['destination_hub_id'])
            ->map(function (Collection $items) {
                $first = $items->first();
                $movementCount = $items->count();

                return [
                    'origin_hub_id' => $first['origin_hub_id'],
                    'origin_hub_name' => $first['origin_hub_name'],
                    'destination_hub_id' => $first['destination_hub_id'],
                    'destination_hub_name' => $first['destination_hub_name'],
                    'movement_count' => $movementCount,
                    'average_duration_hours' => $movementCount > 0 ? round((float) $items->avg('duration_hours'), 2) : 0.0,
                    'total_duration_hours' => round((float) $items->sum('duration_hours'), 2),
                ];
            })
            ->sortByDesc('movement_count')
            ->values();
    }

    private function buildTransitSummary(Collection $allLogs, Collection $history, Collection $completedLogs): array
    {
        $completedMovements = $history->count();

        return [
            'total_movements' => $allLogs->count(),
            'completed_movements' => $completedMovements,
            'ongoing_movements' => $allLogs->whereNotNull('departed_at')->whereNull('arrived_at')->count(),
            'average_duration_hours' => $completedMovements > 0 ? round((float) $history->avg('duration_hours'), 2) : 0.0,
            'total_duration_hours' => round((float) $history->sum('duration_hours'), 2),
            'fastest_duration_hours' => $completedMovements > 0 ? round((float) $history->min('duration_hours'), 2) : null,
            'slowest_duration_hours' => $completedMovements > 0 ? round((float) $history->max('duration_hours'), 2) : null,
            'first_departure_at' => $completedLogs->min('departed_at'),
            'last_arrival_at' => $completedLogs->max('arrived_at'),
            'status_breakdown' => $allLogs->groupBy('status')->map(fn (Collection $logs) => $logs->count())->toArray(),
        ];
    }

    private function formatFleetDetails(Fleet $fleet): array
    {
        return [
            'id' => $fleet->id,
            'plate_number' => $fleet->plate_number,
            'type' => $fleet->type,
            'status' => $fleet->status,
            'capacity' => $fleet->capacity,
            'current_hub' => [
                'id' => $fleet->currentHub?->id,
                'name' => $fleet->currentHub?->name,
            ],
        ];
    }

    private function buildRequestedPackageMap(array $packageIds, array $packageQuantities): array
    {
        $normalizedPackageIds = collect($packageIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->values();

        $normalizedPackageQuantities = collect($packageQuantities)
            ->mapWithKeys(function ($quantity, $id) {
                $packageId = (int) $id;
                $qty = (int) $quantity;

                if ($packageId <= 0 || $qty <= 0) {
                    return [];
                }

                return [$packageId => $qty];
            })
            ->all();

        $quantityByPackageId = [];

        foreach ($normalizedPackageIds as $packageId) {
            $quantityByPackageId[$packageId] = ($quantityByPackageId[$packageId] ?? 0) + 1;
        }

        foreach ($normalizedPackageQuantities as $packageId => $qty) {
            $quantityByPackageId[$packageId] = ($quantityByPackageId[$packageId] ?? 0) + $qty;
        }

        $orderedPackageIds = [];
        $this->appendOrderedUniqueIds($orderedPackageIds, $normalizedPackageIds->all());
        $this->appendOrderedUniqueIds($orderedPackageIds, array_keys($normalizedPackageQuantities));
        $this->appendOrderedUniqueIds($orderedPackageIds, array_keys($quantityByPackageId));

        return [$quantityByPackageId, $orderedPackageIds];
    }

    private function appendOrderedUniqueIds(array &$orderedIds, array $candidateIds): void
    {
        $existing = array_flip($orderedIds);

        foreach ($candidateIds as $candidateId) {
            $packageId = (int) $candidateId;

            if ($packageId <= 0 || isset($existing[$packageId])) {
                continue;
            }

            $orderedIds[] = $packageId;
            $existing[$packageId] = true;
        }
    }

    private function loadPackagesById(Collection $requestedPackageIds): array
    {
        $packagesById = Package::query()
            ->whereIn('id', $requestedPackageIds)
            ->get()
            ->keyBy('id');

        $missingPackageIds = $requestedPackageIds
            ->diff($packagesById->keys())
            ->values()
            ->all();

        return [$packagesById, $missingPackageIds];
    }

    private function buildCalculatedPackages(
        Collection $requestedPackageIds,
        Collection $packagesById,
        array $quantityByPackageId,
        int $volumetricDivisor
    ): Collection {
        return $requestedPackageIds->map(function (int $packageId) use ($packagesById, $quantityByPackageId, $volumetricDivisor) {
            /** @var Package|null $package */
            $package = $packagesById->get($packageId);

            if (!$package) {
                return null;
            }

            $volumeCm3 = (float) ($package->volume ?? ((float) $package->length * (float) $package->width * (float) $package->height));
            $actualWeightKg = (float) $package->weight;
            $volumetricWeightKg = round($volumeCm3 / $volumetricDivisor, 2);
            $chargeableWeightKg = round(max($actualWeightKg, $volumetricWeightKg), 2);

            return [
                'id' => $package->id,
                'tracking_number' => $package->tracking_number,
                'quantity_requested' => (int) ($quantityByPackageId[$package->id] ?? 0),
                'actual_weight_kg' => round($actualWeightKg, 2),
                'volume_cm3' => round($volumeCm3, 2),
                'volumetric_weight_kg' => $volumetricWeightKg,
                'chargeable_weight_kg' => $chargeableWeightKg,
            ];
        })->filter()->values();
    }

    private function summarizePackageWeights(Collection $calculatedPackages, float $capacityKg): array
    {
        $selectedPackageCount = (int) $calculatedPackages->sum('quantity_requested');

        $totalActualWeight = round((float) $calculatedPackages->sum(
            fn (array $item): float => $item['actual_weight_kg'] * $item['quantity_requested']
        ), 2);

        $totalVolumetricWeight = round((float) $calculatedPackages->sum(
            fn (array $item): float => $item['volumetric_weight_kg'] * $item['quantity_requested']
        ), 2);

        $totalChargeableWeight = round((float) $calculatedPackages->sum(
            fn (array $item): float => $item['chargeable_weight_kg'] * $item['quantity_requested']
        ), 2);

        $averageChargeableWeight = $selectedPackageCount > 0
            ? round($totalChargeableWeight / $selectedPackageCount, 2)
            : 0.0;

        $estimatedMaxPackagesByAverage = $averageChargeableWeight > 0
            ? (int) floor($capacityKg / $averageChargeableWeight)
            : 0;

        return [
            'selected_package_count' => $selectedPackageCount,
            'total_actual_weight_kg' => $totalActualWeight,
            'total_volumetric_weight_kg' => $totalVolumetricWeight,
            'total_chargeable_weight_kg' => $totalChargeableWeight,
            'utilization_percentage_if_all_loaded' => $capacityKg > 0
                ? round(($totalChargeableWeight / $capacityKg) * 100, 2)
                : 0.0,
            'can_fit_all_packages' => $totalChargeableWeight <= $capacityKg,
            'over_capacity_kg' => round(max(0, $totalChargeableWeight - $capacityKg), 2),
            'average_chargeable_weight_per_package_kg' => $averageChargeableWeight,
            'estimated_max_packages_by_average' => $estimatedMaxPackagesByAverage,
        ];
    }

    private function buildLoadingSequence(Collection $calculatedPackages, array $orderedPackageIds, string $strategy): Collection
    {
        if ($strategy !== 'keep_order') {
            return $calculatedPackages->sortBy('chargeable_weight_kg')->values();
        }

        return collect($orderedPackageIds)
            ->map(fn (int $id) => $calculatedPackages->firstWhere('id', $id))
            ->filter()
            ->values();
    }

    private function simulateCapacityLoading(Collection $sequence, float $capacityKg): array
    {
        $loadedPackages = [];
        $overflowPackages = [];
        $usedCapacityKg = 0.0;

        foreach ($sequence as $item) {
            $quantityRequested = (int) $item['quantity_requested'];
            $chargeableWeightKg = (float) $item['chargeable_weight_kg'];

            if ($quantityRequested <= 0 || $chargeableWeightKg <= 0) {
                continue;
            }

            $remainingCapacityBefore = max(0, $capacityKg - $usedCapacityKg);
            $maxLoadableQty = (int) floor(($remainingCapacityBefore + 0.00001) / $chargeableWeightKg);

            $loadedQty = max(0, min($quantityRequested, $maxLoadableQty));
            $overflowQty = max(0, $quantityRequested - $loadedQty);

            if ($loadedQty > 0) {
                $loadedChargeableWeight = $loadedQty * $chargeableWeightKg;
                $usedCapacityKg += $loadedChargeableWeight;

                $loadedPackages[] = [
                    ...$item,
                    'loaded_quantity' => $loadedQty,
                    'loaded_chargeable_weight_kg' => round($loadedChargeableWeight, 2),
                ];
            }

            if ($overflowQty > 0) {
                $overflowPackages[] = [
                    ...$item,
                    'overflow_quantity' => $overflowQty,
                    'overflow_chargeable_weight_kg' => round($overflowQty * $chargeableWeightKg, 2),
                ];
            }
        }

        $usedCapacityKg = round($usedCapacityKg, 2);

        return [
            'loaded_packages' => $loadedPackages,
            'overflow_packages' => $overflowPackages,
            'loaded_package_count' => (int) collect($loadedPackages)->sum('loaded_quantity'),
            'overflow_package_count' => (int) collect($overflowPackages)->sum('overflow_quantity'),
            'used_capacity_kg' => $usedCapacityKg,
            'remaining_capacity_kg' => round(max(0, $capacityKg - $usedCapacityKg), 2),
        ];
    }

    private function syncStatusLoadTransition(Fleet $fleet, string $oldStatus, string $newStatus): void
    {
        if (!$fleet->current_hub_id) {
            return;
        }

        if ($oldStatus === 'idle' && $newStatus === 'in_transit') {
            $this->syncHubWarehouseLoad((int) $fleet->current_hub_id, -((int) $fleet->capacity));
            return;
        }

        if ($oldStatus === 'in_transit' && $newStatus === 'idle') {
            $this->syncHubWarehouseLoad((int) $fleet->current_hub_id, (int) $fleet->capacity);
        }
    }

    private function logFleetRelocation(Fleet $fleet, int $originHubId, int $destinationHubId): void
    {
        FleetLog::create([
            'fleet_id' => $fleet->id,
            'origin_hub_id' => $originHubId,
            'destination_hub_id' => $destinationHubId,
            'status' => 'arrived',
            'departed_at' => now()->subHours(random_int(1, 10)),
            'arrived_at' => now(),
        ]);
    }

    private function syncHubWarehouseLoad(int $hubId, int $delta): void
    {
        if ($delta === 0) {
            return;
        }

        $warehouses = Warehouse::where('hub_id', $hubId)->get();

        if ($warehouses->isEmpty()) {
            return;
        }

        $delta < 0
            ? $this->deductWarehouseLoads($warehouses, abs($delta))
            : $this->fillWarehouseLoads($warehouses, $delta);
    }

    private function deductWarehouseLoads(Collection $warehouses, int $targetLoad): int
    {
        $remaining = $targetLoad;
        $applied = 0;

        foreach ($warehouses->sortByDesc('current_load') as $warehouse) {
            if ($remaining <= 0) {
                break;
            }

            $currentLoad = (int) $warehouse->current_load;
            if ($currentLoad <= 0) {
                continue;
            }

            $deduct = min($currentLoad, $remaining);
            if ($deduct <= 0) {
                continue;
            }

            $warehouse->decrement('current_load', $deduct);
            $remaining -= $deduct;
            $applied += $deduct;
        }

        return $applied;
    }

    private function fillWarehouseLoads(Collection $warehouses, int $targetLoad): int
    {
        $remaining = $targetLoad;
        $applied = 0;

        foreach ($warehouses->sortByDesc(function (Warehouse $warehouse): int {
            return max(0, (int) $warehouse->capacity - (int) $warehouse->current_load);
        }) as $warehouse) {
            if ($remaining <= 0) {
                break;
            }

            $availableSpace = max(0, (int) $warehouse->capacity - (int) $warehouse->current_load);
            if ($availableSpace <= 0) {
                continue;
            }

            $add = min($availableSpace, $remaining);
            if ($add <= 0) {
                continue;
            }

            $warehouse->increment('current_load', $add);
            $remaining -= $add;
            $applied += $add;
        }

        return $applied;
    }

    private function getVolumetricDivisorByFleetType(string $fleetType): int
    {
        return match ($fleetType) {
            'motorcycle' => 3500,
            'van' => 4500,
            default => 6000,
        };
    }
}
