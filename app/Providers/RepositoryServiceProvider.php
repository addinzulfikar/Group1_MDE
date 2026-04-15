<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\FleetRepositoryInterface;
use App\Repositories\Eloquent\FleetRepository;
use App\Repositories\Contracts\HubRepositoryInterface;
use App\Repositories\Eloquent\HubRepository;
use App\Repositories\Contracts\ShipmentRepositoryInterface;
use App\Repositories\Eloquent\ShipmentRepository;
use App\Repositories\Contracts\TrackingRepositoryInterface;
use App\Repositories\Eloquent\TrackingRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(FleetRepositoryInterface::class, FleetRepository::class);
        $this->app->bind(HubRepositoryInterface::class, HubRepository::class);
        $this->app->bind(ShipmentRepositoryInterface::class, ShipmentRepository::class);
        $this->app->bind(TrackingRepositoryInterface::class, TrackingRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
