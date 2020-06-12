# PHP开源商城

[效果浏览](#浏览效果)

PHP>=7.2.5\
Laravel7\


## 安装

1、docker安装
进入docker目录执行如下命令进行安装
````
docker-compose up [-d 可选参数用于后台执行]
````

安装依赖包
````
docker exec -it shop-php composer install
````
生成key
````
docker exec -it shop-php php artisan key:generate
````
配置目录权限
````
docker exec -it shop-php chmod 777 -R storage
````
数据填充
````
docker exec -it shop-php php artisan shop:install
````
启动队列
````
docker exec -it shop-php php artisan queue:work
````
2、普通安装

安装依赖包
````
composer install
````
生成key
````
php artisan key:generate
````

配置目录权限
`确保storage目录有777权限` 

数据填充
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


# 浏览效果

首页
![avatar](http://qbniinxdj.bkt.clouddn.com/product-index.png)

详情页
![avatar](http://qbniinxdj.bkt.clouddn.com/product-detail.png)

购物车
![avatar](http://qbniinxdj.bkt.clouddn.com/product-cart.png)

分类
![avatar](http://qbniinxdj.bkt.clouddn.com/product-category.png)

订单
![avatar](http://qbniinxdj.bkt.clouddn.com/product-category.png)
订单支付
![avatar](http://qbniinxdj.bkt.clouddn.com/product-payment.png)


