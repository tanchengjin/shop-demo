<?php

namespace App\Providers;

use App\Category;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Yansongda\Pay\Pay;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('alipay', function () {
            $config = config('shop.alipay');
            $config['notify_url'] = env('ALI_NOTIFY_URL', route('payment.alipay.notify'));
            $config['return_url'] = env('ALI_RETURN_URL', route('payment.alipay.return'));
            return Pay::alipay($config);
        });

        $this->app->singleton('wechat', function () {
            $config = config('shop.wechat');
            $config['notify_url'] = 'http://yanda.net.cn/notify.php';
            return Pay::wechat($config);
        });

        #elastic
        $this->app->singleton('es',function(){
            $builder=ClientBuilder::create()->setHosts(config('database.elasticsearch.hosts'));

            if(app()->environment() === 'local'){
                $builder->setLogger(app('log')->getLogger());
            }

            return $builder->build();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(199);

        view()->share('categoryTree',Category::categoryTree());
    }
}
