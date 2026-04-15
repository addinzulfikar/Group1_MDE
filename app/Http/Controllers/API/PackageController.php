<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePackageRequest;
use App\Http\Requests\UpdatePackageRequest;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Display a listing of all packages.
     */
    public function index()
    {
        try {
            $packages = Package::with('warehouse')->get();

            // Add dimension category to each package
            $packages = $packages->map(function ($package) {
                return [
                    ...$package->toArray(),
                    'dimension_category' => $package->getDimensionCategory()
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Package list retrieved successfully',
                'data' => $packages,
                'total' => $packages->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve package list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created package in storage.
     */
    public function store(StorePackageRequest $request)
    {
        try {
            // Auto calculate volume: length × width × height
            $volume = $request->length * $request->width * $request->height;

            $package = Package::create([
                ...$request->validated(),
                'volume' => $volume
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Package registered successfully',
                'data' => [
                    ...$package->toArray(),
                    'dimension_category' => $package->getDimensionCategory()
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register package',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified package.
     */
    public function show($id)
    {
        try {
            $package = Package::with('warehouse')->findOrFail($id);

            // Convert to array for response
            $packageData = $package->toArray();
            
            // Ensure 'id' exists
            if (!isset($packageData['id'])) {
                $packageData['id'] = $package->id;
            }

            $packageData['dimension_category'] = $package->getDimensionCategory();

            return response()->json([
                'success' => true,
                'message' => 'Package retrieved successfully',
                'data' => $packageData
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Package not found',
                'error' => 'The package with ID ' . $id . ' does not exist'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve package',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified package in storage.
     */
    public function update(UpdatePackageRequest $request, $id)
    {
        try {
            $package = Package::findOrFail($id);

            $data = $request->validated();

            // Recalculate volume if any dimension is updated
            if ($request->has('length') || $request->has('width') || $request->has('height')) {
                $length = $data['length'] ?? $package->length;
                $width = $data['width'] ?? $package->width;
                $height = $data['height'] ?? $package->height;
                $data['volume'] = $length * $width * $height;
            }

            $package->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Package updated successfully',
                'data' => [
                    ...$package->toArray(),
                    'dimension_category' => $package->getDimensionCategory()
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Package not found',
                'error' => 'The package with ID ' . $id . ' does not exist'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update package',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified package from storage.
     */
    public function destroy($id)
    {
        try {
            $package = Package::findOrFail($id);

            $package->delete();

            return response()->json([
                'success' => true,
                'message' => 'Package deleted successfully',
                'data' => $package
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Package not found',
                'error' => 'The package with ID ' . $id . ' does not exist'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete package',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dimension details and category of a package.
     */
    public function getDimension($id)
    {
        try {
            $package = Package::findOrFail($id);

            $dimensionCategory = $package->getDimensionCategory();

            return response()->json([
                'success' => true,
                'message' => 'Package dimension retrieved successfully',
                'data' => [
                    'id' => $package->id,
                    'tracking_number' => $package->tracking_number,
                    'length' => $package->length,
                    'width' => $package->width,
                    'height' => $package->height,
                    'volume' => $package->volume,
                    'dimension_category' => $dimensionCategory,
                    'category_description' => $this->getCategoryDescription($dimensionCategory)
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Package not found',
                'error' => 'The package with ID ' . $id . ' does not exist'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dimension',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get description for dimension category.
     */
    private function getCategoryDescription($category)
    {
        $descriptions = [
            'small' => 'Volume ≤ 1000 cm³',
            'medium' => 'Volume 1000 - 5000 cm³',
            'large' => 'Volume > 5000 cm³'
        ];

        return $descriptions[$category] ?? 'Unknown category';
    }
}