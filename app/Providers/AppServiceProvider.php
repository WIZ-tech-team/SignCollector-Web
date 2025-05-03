<?php

namespace App\Providers;

use App\Models\SubscriptionItem;
use Illuminate\Support\ServiceProvider;
use Laravel\Paddle\Cashier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Cashier::useSubscriptionItemModel(SubscriptionItem::class);
        
        // Event::listen(
        //     TransactionCompleted::class,
        //     TransactionCompletedListener::class
        // );

        // Event::listen(
        //     TransactionUpdated::class,
        //     TransactionUpdatedListener::class
        // );

        // Event::listen(
        //     ProductCreated::class,
        //     ProductCreatedListener::class
        // );

        // Event::listen(
        //     ProductUpdated::class,
        //     ProductUpdatedListener::class
        // );

    }
}
