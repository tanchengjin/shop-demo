<?php

namespace App\Http\Requests;

use App\Order;
use App\Product;
use App\ProductSku;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SeckillOrderRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'address_id' => ['required', function () {
                Rule::exists('user_addresses', 'id')->where('user_id', request()->user()->id);
            }],
            'sku_id' => ['required', function ($attribute, $value, $fail) {
                if (!$sku = ProductSku::find($value)) {
                    return $fail('该商品不存在');
                }

                if (!$sku->product->on_sale) {
                    return $fail('商品未商家');
                }
                if ($sku->product->type !== Product::TYPE_SECKILL) {
                    return $fail('该商品不是秒杀商品');
                }

                if ($sku->stock === 0) {
                    return $fail('商品已售完!');
                }

                if ($order = Order::query()->where('user_id', $this->user()->id)
                    ->whereHas('item', function ($query) use ($value) {
                        $query->where('product_sku_id', $value);
                    })->where(function ($query) {
                        $query->whereNotNull('paid_at')->orWhere('closed', 0);
                    })->first()) {
                    if ($order->paid_at) {
                        return $fail('您已抢购了该商品');
                    }
                    return $fail('您已下单,请支付');
                }
            }],
        ];
    }

    public function messages()
    {
        return [
            'sku_id.required'=>'请选择商品',
            'address_id.required'=>'请选择收货地址'
        ];
    }
}
