<?php

namespace App\Listeners\Cashier;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Laravel\Paddle\Events\TransactionUpdated;
use Laravel\Paddle\Transaction;

class TransactionUpdatedListener
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
    public function handle(TransactionUpdated $event): void
    {

        // $dataMap = [
        //     'billable_type' => $event->billable->getMorphClass(),
        //     'paddle_id' => $event->payload['data']['id'],
        //     'paddle_subscription_id' => $event->payload['data']['subscription_id'],
        //     'invoice_number' => $event->payload['data']['invoice_number'],
        //     'status' => $event->payload['data']['status'],
        //     'total' => $event->payload['data']['details']['totals']['total'],
        //     'tax' => $event->payload['data']['details']['totals']['tax'],
        //     'currency' => $event->payload['data']['currency_code'],
        //     'billed_at' => Carbon::parse($event->payload['data']['billed_at'])->format('Y-m-d H:i:s')
        // ];

        // Transaction::where('paddle_id', $dataMap['paddle_id'])->update($dataMap);

        DB::table('cashier_paddle_received_events')->insert([
            'event' => TransactionUpdated::class,
            'meta' => json_encode($event),
            'created_at' => now(),
            'updated_at' => now()
        ]);

    }
}
