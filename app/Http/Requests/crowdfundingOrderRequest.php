<?php

namespace App\Http\Requests;

use App\CrowdfundingProduct;
use App\Product;
use App\ProductSku;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class crowdfundingOrderRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sku_id' => ['required', function ($attribute, $value, $fail) {
                if (!$sku = ProductSku::query()->find($value)) {
                    return $fail('商品不存在');
                }
                if (!$sku->product->on_sale) {
                    return $fail('该商品已下架');
                }

                if ($sku->product->type !== Product::TYPE_CROWDFUNDING) {
                    return $fail('该商品不支持众筹');
                }

                if ($sku->product->crowdfunding->status !== CrowdfundingProduct::STATUS_FUNDING) {
                    return $fail('众筹已结束');
                }
                if ($sku->stock == 0) {
                    return $fail('库存已售完');
                }

                if ($this->input('amount') > 0 && $this->input('amount') > $sku->stock) {
                    return $fail('库存不足');
                }
                if ($sku->product->crowdfunding->end_at->lt(Carbon::now())) {
                    return $fail('该商品已过期');
                }
            }],
            'amount' => ['required', 'min:1', 'integer'],
            'address_id' => ['required', Rule::exists('user_addresses', 'id')->where('user_id', $this->user()->id)],
        ];
    }
}
