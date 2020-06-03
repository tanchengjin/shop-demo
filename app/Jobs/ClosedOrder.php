<?php

namespace App\Jobs;

use App\Order;
use App\ProductSku;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ClosedOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     *
     * @param Order $order
     * @param null|int $ttl
     */
    public function __construct(Order $order, int $ttl = null)
    {
        $this->order = $order;
        if (is_null($ttl)) {
            $ttl = config('shop.order.order_ttl');
        }
        $this->delay($ttl);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::transaction(function () {
            foreach ($this->order->item as $item){
                $item->sku->increment('stock',$item['amount']);
            }
            $this->order->update([
                'closed' => 1
            ]);
        });
    }
}
