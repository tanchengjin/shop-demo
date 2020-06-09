<?php

namespace App\Listeners;

use App\Events\OrderReviewed;
use App\OrderItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class UpdateReviewRating
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param OrderReviewed $event
     * @return void
     */
    public function handle(OrderReviewed $event)
    {
        $order = $event->getOrder();
        $items = $order->item()->with(['product'])->get();
        foreach ($items as $item) {
            $data = OrderItem::query()->where('product_id', $item->product->id)
                ->whereNotNull('reviewed_at')
                ->whereHas('order', function ($query) {
                    $query->whereNotNull('paid_at');
                })->first([
                    DB::raw('count(*) as reviewed_count'),
                    DB::raw('avg(rating) as reviewed_rating')
                ]);
            $item->product->update([
                'rating'=>$data->reviewed_rating,
                'review_count'=>$data->reviewed_count
            ]);
        }
    }
}
