<?php

namespace App\Console\Commands\Cron;

use App\CrowdfundingProduct;
use App\Jobs\CrowdfundingFail;
use App\Order;
use App\Product;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Yansongda\Pay\Log;

class CrowdfundingFinish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:crowdfunding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '众筹结束';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        CrowdfundingProduct::query()
            ->with(['product'])
            ->where('end_at', '<=', Carbon::now())
            ->where('status', CrowdfundingProduct::STATUS_FUNDING)
            ->get()
            ->each(function (CrowdfundingProduct $data) {
                if ($data->current_amount >= $data->target_amount) {
                    $this->crowdfundingSuccess($data);
                } else {
                    $this->crowdfundingFailed($data);
                }
            });
    }

    public function crowdfundingSuccess(CrowdfundingProduct $crowdfunding)
    {
        $crowdfunding->update([
            'status' => CrowdfundingProduct::STATUS_SUCCESS
        ]);
    }

    public function crowdfundingFailed(CrowdfundingProduct $crowdfunding)
    {
        $crowdfunding->update([
            'status' => CrowdfundingProduct::STATUS_FAIL
        ]);
        dispatch(new CrowdfundingFail($crowdfunding));
    }
}
