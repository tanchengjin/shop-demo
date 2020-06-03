<?php


namespace App\Services;


use App\Jobs\ClosedOrder;
use App\Order;
use App\OrderItem;
use App\ProductSku;
use App\ShoppingCart;
use App\UserAddress;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function store($items, $address_id, $remark=null)
    {
        $order=DB::transaction(function() use($items,$address_id,$remark){
            $address=UserAddress::query()->find($address_id);
            $address->last_used_at=Carbon::now();
            $order=new Order([
                'total_amount'=>0,
                'address'=>[
                    'province'=>$address->province,
                    'city'=>$address->city,
                    'district'=>$address->district,
                    'zip'=>$address->zip,
                    'contact_name'=>$address->contact_name,
                    'contact_phone'=>$address->contact_phone
                ],
            ]);
            if(!is_null($remark)){
                $order->remark=$remark;
            }
            $order->user()->associate(Auth::id());

            $order->save();

            $total_amount=0;
            foreach($items as $item){
                $sku=ProductSku::query()->find($item['sku_id']);
                $orderItem=$order->item()->make([
                    'amount'=>$item['amount'],
                    'price'=>$sku->price,
                ]);
                $orderItem->product()->associate($sku->product);
                $orderItem->sku()->associate($sku);
                $orderItem->save();
                $total_amount+=$item['amount']*$sku->price;
                if($sku->decrementStock($item['amount']) <= 0){
                    throw new \Exception('库存不足');
                }

            }

            $order->update([
                'total_amount'=>$total_amount
            ]);
            $ids=collect($order->item)->pluck('product_sku_id')->all();

            (new ShoppingCartService())->remove($ids);

            return $order;
        });
        dispatch(new ClosedOrder($order,30));
        return $order;
    }
}
