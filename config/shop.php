<?php
return [
    'order' => [
        #order下单后有效期,单位秒,默认30分钟
        'order_ttl' => env('ORDER_TTL', 1800),
        //秒杀订单有效期,默认5分钟
        'seckill_ttl' => env('SECKILL_TTL', 300)
    ],
    #支付宝支付配置
    'alipay' => [
        'app_id' => env('ALI_APP_ID', ''),
        'ali_public_key' => env('ALI_PUBLIC_KEY', ''),
        // 加密方式： **RSA2**
        'private_key' => env('ALI_PRIVATE_KEY', ''),
        // 使用公钥证书模式，请配置下面两个参数，同时修改ali_public_key为以.crt结尾的支付宝公钥证书路径，如（./cert/alipayCertPublicKey_RSA2.crt）
        // 'app_cert_public_key' => './cert/appCertPublicKey.crt', //应用公钥证书路径
        // 'alipay_root_cert' => './cert/alipayRootCert.crt', //支付宝根证书路径
        'log' => [ // optional
            'file' => storage_path('logs/alipay/alipay.log'),
            'level' => 'debug', // 建议生产环境等级调整为 info，开发环境为 debug
//            'type' => 'single', // optional, 可选 daily.
//            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
    ],

    'wechat' => [
        'appid' => env('WECHAT_APP_ID', ''), // APP APPID
        'app_id' => env('WECHAT_APP_ID', ''), // 公众号 APPID
        'miniapp_id' => '', // 小程序 APPID
        'mch_id' => env('WECHAT_MCH_ID'),
        'key' => '',
        'cert_client' => resource_path('wechat/') . env('WECHAT_CERT', ''), // optional，退款等情况时用到
        'cert_key' => resource_path('wechat/') . env('WECHAT_KEY', ''),// optional，退款等情况时用到
        'log' => [ // optional
            'file' => storage_path('logs/wechat/wechat.log'),
            'level' => 'debug', // 建议生产环境等级调整为 info，开发环境为 debug
            'type' => 'single', // optional, 可选 daily.
            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // optional, dev/hk;当为 `hk` 时，为香港 gateway。
    ],
];
