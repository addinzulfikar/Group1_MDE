<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\FleetRepositoryInterface;
use App\Repositories\Eloquent\FleetRepository;
use App\Repositories\Contracts\HubRepositoryInterface;
use App\Repositories\Eloquent\HubRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(FleetRepositoryInterface::class, FleetRepository::class);
        $this->app->bind(HubRepositoryInterface::class, HubRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
