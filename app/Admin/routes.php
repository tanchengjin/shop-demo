<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');


    $router->get('orders','OrderController@index')->name('admin.orders.index');
    $router->get('orders/{id}','OrderController@show')->name('admin.orders.show');
    $router->post('orders/{order}/ship','OrderController@ship')->name('admin.orders.ship');
    $router->post('orders/{order}/refund','OrderController@handleRefund')->name('admin.orders.refund');
    $router->resource('products','ProductController');

});
