<?php
return [
    'order' => [
        #order下单后有效期,秒
        'order_ttl' => env('ORDER_TTL', 300)
    ],
    #支付宝支付配置
    'alipay' => [
        'app_id' => env('ALI_APP_ID',''),
        'ali_public_key' => env('ALI_PUBLIC_KEY',''),
        // 加密方式： **RSA2**
        'private_key' => env('ALI_PRIVATE_KEY',''),
        // 使用公钥证书模式，请配置下面两个参数，同时修改ali_public_key为以.crt结尾的支付宝公钥证书路径，如（./cert/alipayCertPublicKey_RSA2.crt）
        // 'app_cert_public_key' => './cert/appCertPublicKey.crt', //应用公钥证书路径
        // 'alipay_root_cert' => './cert/alipayRootCert.crt', //支付宝根证书路径
        'log' => [ // optional
            'file' => storage_path('logs/alipay/').date('Y-m-d').'.log',
            'level' => 'debug', // 建议生产环境等级调整为 info，开发环境为 debug
//            'type' => 'single', // optional, 可选 daily.
//            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
    ],
];
