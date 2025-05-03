<?php

namespace App\Listeners\Paddle;

use App\Services\PaddleProductsManagementService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Paddle\SDK\Notifications\Events\PriceUpdated;

class PriceUpdatedListener
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
    public function handle(PriceUpdated $event): void
    {
        DB::table('cashier_paddle_received_events')->insert([
            'event' => PriceUpdated::class,
            'meta' => json_encode($event),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $price = $event->price;

        $this->paddleProductsService->storePlanPrice($price);
        
    }
}
