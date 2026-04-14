<?php

namespace App\Repositories\Contracts;

interface FleetRepositoryInterface
{
    public function getAllFleets($search = null);
    public function getFleetById($id);
    public function calculateTransitDuration($fleetId);
    public function storeFleet(array $data);
    public function updateFleetStatus($id, $status);
    public function relocateFleet($id, $newHubId);
}
