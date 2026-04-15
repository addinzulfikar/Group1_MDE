<?php

namespace App\Providers;

use App\Repositories\Contracts\AuthTokenRepositoryInterface;
use App\Repositories\Contracts\ShippingProfileRepositoryInterface;
use App\Repositories\Contracts\ShippingRateRuleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\EloquentAuthTokenRepository;
use App\Repositories\Eloquent\EloquentShippingProfileRepository;
use App\Repositories\Eloquent\EloquentShippingRateRuleRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(AuthTokenRepositoryInterface::class, EloquentAuthTokenRepository::class);
        $this->app->bind(ShippingProfileRepositoryInterface::class, EloquentShippingProfileRepository::class);
        $this->app->bind(ShippingRateRuleRepositoryInterface::class, EloquentShippingRateRuleRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
