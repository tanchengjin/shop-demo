<?php

namespace App\Jobs;

use App\CrowdfundingProduct;
use App\Order;
use App\Product;
use App\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CrowdfundingFail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $crowdfunding;

    /**
     * CrowdfundingFail constructor.
     * @param CrowdfundingProduct $crowdfundingProduct
     */
    public function __construct(CrowdfundingProduct $crowdfundingProduct)
    {
        $this->crowdfunding = $crowdfundingProduct;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        if ($this->crowdfunding->status !== CrowdfundingProduct::STATUS_FAIL) {
            return;
        }

        $orderService = new OrderService();
        Order::query()->where('type', Product::TYPE_CROWDFUNDING)
            ->whereNotNull('paid_at')
            ->whereHas('item', function ($query) {
                $query->where('product_id', $this->crowdfunding->product_id);
            })->get()->each(function (Order $order) use ($orderService) {
                $orderService->handleRefund($order);
            });
    }
}
