<?php

namespace App\Listeners\Paddle;

use App\Models\Plan;
use App\Services\PaddleProductsManagementService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Paddle\SDK\Notifications\Events\ProductUpdated;

class ProductUpdatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected PaddleProductsManagementService $paddleProductsService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProductUpdated $event): void
    {

        DB::table('cashier_paddle_received_events')->insert([
            'event' => ProductUpdated::class,
            'meta' => json_encode($event),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        $this->paddleProductsService->storePlan($event->product);
        
    }
}
