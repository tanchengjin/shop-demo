<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('products.index');
});

Route::post('alipay/notify', 'PaymentController@alipayNotify')->name('payment.alipay.notify');

Route::get('products', 'ProductController@index')->name('products.index');
Route::get('products/{id}', 'ProductController@show')->name('products.show');

//authorize..
Route::group(['middleware' => ['auth']], function () {
    #商品收藏
    Route::post('products/{product}/favorite', 'ProductController@favorite')->name('products.favorite');
    Route::delete('products/{product}/disfavor', 'ProductController@disfavor')->name('products.disfavor');
    #购物车
    Route::get('shoppingCart', 'ShoppingCartController@index')->name('carts.index');
    Route::post('shoppingCart', 'ShoppingCartController@store')->name('carts.store');
    Route::delete('shoppingCart/{id}', 'ShoppingCartController@destroy')->name('carts.destroy');

    Route::post('order', 'OrderController@store')->name('order.store');
    Route::post('crowdfundingOrder', 'OrderController@crowdfundingStore')->name('order.crowdfunding');
    Route::post('seckillOrder', 'OrderController@seckill')->name('order.seckill');
    //==============用户中心开始==================
    #==订单==
    Route::get('orders', 'OrderController@index')->name('orders.index');
    Route::get('orders/{order}', 'OrderController@show')->name('orders.show');
    Route::get('orders/{order}/payment/confirm', 'OrderController@payment')->name('orders.payment');
    Route::post('orders/{order}/received', 'OrderController@received')->name('orders.received');
    Route::post('orders/{order}/refund', 'OrderController@refund')->name('orders.refund');
    Route::get('orders/{order}/review', 'OrderController@reviewIndex')->name('orders.review.index');
    Route::post('orders/{order}/review', 'OrderController@review')->name('orders.review.store');

    #收藏列表
    Route::get('center/favorites', 'ProductController@favorList')->name('products.favorite.index');

    #==用户收货地址==
    Route::get('center/address', 'AddressController@index')->name('user.addresses.index');
    Route::get('center/address/create', 'AddressController@create')->name('user.addresses.create');
    Route::post('center/address/create', 'AddressController@store')->name('user.addresses.store');
    Route::get('center/address/{address}', 'AddressController@edit')->name('user.addresses.edit');
    Route::put('center/address/{address}', 'AddressController@update')->name('user.addresses.update');
    Route::delete('center/address/{address}', 'AddressController@destroy')->name('user.addresses.destroy');
    //==============用户中心结束==================
    #支付宝支付
    Route::get('alipay/return', 'PaymentController@alipayReturn')->name('payment.alipay.return');
    Route::get('alipay/{order}', 'PaymentController@alipay')->name('payment.alipay');
    Route::post('alipay/{order}/refund', 'PaymentController@alipayRefund')->name('payment.alipay.refund');

    #微信支付
    Route::get('wechat/{order}', 'PaymentController@wechat')->name('payment.wechat');

    #优惠券
    Route::get('coupon', 'CouponController@index')->name('coupon.index');
    Route::post('coupon', 'CouponController@acquireCoupon')->name('coupon.acquire');

});
Route::post('wechat/notify', 'PaymentController@wechatNotify')->name('payment.wechat.notify');

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');

