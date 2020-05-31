<?php

namespace App\Http\Requests;

use App\ProductSku;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class ShoppingCartRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id'=>['required',function($key,$value,$fail){
                $sku=ProductSku::query()->find($value);
                if(!$sku){
                    return $fail(__('商品不存在'));
                }

                if(!$sku->product->on_sale){
                    return $fail(__('该商品未上架'));
                }

                if($sku->stock == 0){
                    return $fail(__('商品库存不足'));
                }
                $amount=$this->input('amount')?:0;

                if($amount > 0 && $amount > $sku->stock){
                    return $fail(__('商品购买数超过库存'));
                }
            }],
            'amount'=>['required','min:1','numeric'],
        ];
    }

//    protected function failedValidation(Validator $validator)
//    {
//        throw (new HttpResponseException(response()->json([
//            'errno'=>500,
//            'message'=>$validator->getMessageBag()->first(),
//            'data'=>[]
//        ],422)));
//    }
}
