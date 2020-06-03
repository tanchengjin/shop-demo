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


Route::get('products','ProductController@index')->name('products.index');
Route::get('products/{id}','ProductController@show')->name('products.show');

//authorize..
Route::group(['middleware'=>['auth']],function(){
    Route::get('shoppingCart','ShoppingCartController@index')->name('carts.index');
    Route::post('shoppingCart','ShoppingCartController@store')->name('carts.store');
    Route::delete('shoppingCart/{id}','ShoppingCartController@destroy')->name('carts.destroy');

    Route::post('order','OrderController@store')->name('order.store');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
