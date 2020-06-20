<?php

namespace App\Providers;

use App\Contracts\LoginInterface;
use App\Services\NewIdpIntegrationService;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(LoginInterface::class, NewIdpIntegrationService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
