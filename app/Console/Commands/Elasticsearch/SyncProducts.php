<?php

namespace App\Console\Commands\Elasticsearch;

use App\Product;
use Illuminate\Console\Command;

class SyncProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'es:syncProducts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步商品信息至elasticsearch';

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
        $es = app('es');

        Product::query()->with(['sku', 'properties'])
            ->chunkById(100, function ($products) use ($es) {
                $this->info(sprintf('正在同步 索引为%s~%s的数据', $products->first()->id, $products->last()->id));

                $res = [
                    'body' => []
                ];

                foreach ($products as $product) {
                    $data = $product->toESArray();

                    $res['body'][] = [
                        'index' => [
                            '_index' => 'products',
                            '_type' => '_doc',
                            '_id' => $data['id']
                        ],
                    ];

                    $res['body'][] = $data;
                }

                try {
                    $es->bulk($res);
                } catch (\Exception $exception) {
                    $this->error($exception->getMessage());
                }
            });
        $this->info('同步完成');
    }
}
