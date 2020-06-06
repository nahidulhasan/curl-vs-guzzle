<?php

namespace App\Providers;

use App\Contracts\ApiBaseServiceInterface;
use App\Services\ApiBaseService;
use Illuminate\Support\ServiceProvider;

class ApiBaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ApiBaseServiceInterface::class, ApiBaseService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
