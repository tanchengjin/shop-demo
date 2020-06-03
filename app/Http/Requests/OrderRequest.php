<?php

namespace App\Http\Requests;

use App\ProductSku;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'address_id'=>['required','numeric',Rule::exists('user_addresses','id')->where('user_id',$this->user()->id)
                ],
            'items'=>['required','array'],
            'items.*.sku_id'=>['required','numeric',function($key,$value,$fail){
                if(!$sku=ProductSku::query()->find($value)){
                    return $fail('该商品不存在');
                }

                if(!$sku->product->on_sale){
                    return $fail('该商品已下架!');
                }

                if($sku->stock === 0){
                    return $fail('商品库存不足!');
                }
                preg_match('/items.(\d+).sku_id/',$key,$m);
                $amount=$this->input('items')[$m[1]]['amount'];
                if($amount <=0 || $amount > $sku->stock){
                    return $fail(sprintf('商品%s购买数超出库存!',$sku->title));
                }
            }],
            'items.*.amount'=>['required','min:1','numeric'],
        ];
    }
}
