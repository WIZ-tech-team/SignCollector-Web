<?php

namespace App\Exports;

use Laravel\Paddle\Subscription;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SubscriptionsExport implements FromCollection, WithHeadings
{

    protected $from;
    protected $to;

    public function __construct($from_date, $to_date)
    {
        $this->from = $from_date;
        $this->to = $to_date;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $subscriptions = collect();

        Subscription::whereBetween('created_at', [$this->from, $this->to])
            ->with('items.plan', 'transactions', 'billable')
            ->chunk(100, function ($chunk) use ($subscriptions) {
                $chunk->each(function ($subscription) use ($subscriptions) {

                    $plansTxt = $subscription->items
                        ->map(fn($item) => $item->plan->name ?? '')
                        ->filter()
                        ->join(",\n");

                    $transTxt = $subscription->transactions
                        ->map(fn($trans) => sprintf(
                            "%.2f %s - %s",
                            $trans->total / 100,
                            $trans->currency,
                            $trans->status
                        ))
                        ->join(",\n");

                    $subscriptions->push([
                        $subscription->id,
                        $subscription->billable?->name,
                        $subscription->type,
                        $subscription->status,
                        $subscription->trial_ends_at,
                        $subscription->paused_at,
                        $subscription->ends_at,
                        $subscription->created_at,
                        $plansTxt,
                        $transTxt
                    ]);

                });
            });

        return $subscriptions;
    }

    public function headings(): array
    {
        return [
            'ID',
            'User',
            'Type',
            'Status',
            'Trial End',
            'Paused Date',
            'Ends At',
            'Created At',
            'Plans',
            'Transactions'
        ];
    }
}
