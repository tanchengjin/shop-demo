<?php


namespace App\Services;


use App\ShoppingCart;
use Illuminate\Support\Facades\Auth;

class ShoppingCartService
{
    public function add($sku_id, $amount)
    {
        if($cart=ShoppingCart::query()->where('product_sku_id',$sku_id)->first()){
            $cart->update([
                'amount'=>$cart->amount+$amount
            ]);
        }else{
            $cart=new ShoppingCart();
            $cart->user()->associate(Auth::user());
            $cart->product_sku_id=$sku_id;
            $cart->amount=$amount;
            $cart->save();
        }

        return $cart;
    }
}
