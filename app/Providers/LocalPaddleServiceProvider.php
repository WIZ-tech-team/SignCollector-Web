<?php

namespace App\Providers;

use App\Services\PaddleProductsManagementService;
use Illuminate\Support\ServiceProvider;

class LocalPaddleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PaddleProductsManagementService::class, function ($app) {
            return new PaddleProductsManagementService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
