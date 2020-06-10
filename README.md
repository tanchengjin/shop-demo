# PHP开源商城

安装
````
php artisan shop:install
````

启动队列
````
php artisan queue:work
````

### 配置
- 支付宝\

.env配置\
```
ALI_APP_ID= app id
ALI_PUBLIC_KEY= 公钥
ALI_PRIVATE_KEY= 私钥
```

- 微信

.env配置
````
WECHAT_APP_ID= 公众号 app id
WeCHAT_MCH_ID= 商户号
WeCHAT_KEY= API 密钥
````
配置API证书
`将cert与key文件放入到resource/wechat文件夹下，并在.env下配置`

````
WECHAT_CERT=cert文件完整文件名
WECHAT_CERT_KEY=文件完整文件名
````
