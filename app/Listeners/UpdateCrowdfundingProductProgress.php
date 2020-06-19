<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Order;
use App\Product;
use Illuminate\Support\Facades\DB;

class UpdateCrowdfundingProductProgress
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
     * @param  OrderPaid  $event
     * @return void
     */
    public function handle(OrderPaid $event)
    {
        $order=$event->getOrder();

        if($order->type === Product::TYPE_NORMAL){
            return;
        }

        $crowdfunding=$order->item[0]->product->crowdfunding;

        $data=Order::query()->where('type',Product::TYPE_CROWDFUNDING)
            ->whereNotNull('paid_at')
            ->whereHas('item',function($item) use($crowdfunding){
                $item->where('product_id',$crowdfunding->product_id);
            })->first([
                DB::raw('sum(total_amount) as total_amount'),
                DB::raw('count(distinct(user_id)) as user_count'),
            ]);

        $crowdfunding->update([
            'current_amount'=>$data->total_amount,
            'user_count'=>$data->user_count,
        ]);
    }
}
