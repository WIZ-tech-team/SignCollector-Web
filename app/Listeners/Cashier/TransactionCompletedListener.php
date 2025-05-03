<?php

namespace App\Listeners\Cashier;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Laravel\Paddle\Events\TransactionCompleted;

class TransactionCompletedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TransactionCompleted $event): void
    {
        // $user = $event->billable;
        // $transaction = $event->transaction;
        // $user->increment('credits', $transaction->credits);

        DB::table('cashier_paddle_received_events')->insert([
            'event' => TransactionCompleted::class,
            'meta' => json_encode($event),
            'created_at' => now(),
            'updated_at' => now()
        ]);

    }
}
