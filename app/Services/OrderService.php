<?php


namespace App\Services;


use App\Coupon;
use App\Exceptions\CouponCodeException;
use App\Jobs\ClosedOrder;
use App\Order;
use App\OrderItem;
use App\Product;
use App\ProductSku;
use App\ShoppingCart;
use App\User;
use App\UserAddress;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function store($items, $address_id, $remark = null, Coupon $coupon = null)
    {
        $order = DB::transaction(function () use ($items, $address_id, $remark, $coupon) {

            if ($coupon) {
                $coupon->checkCouponCode();
            }


            $address = UserAddress::query()->find($address_id);
            $address->last_used_at = Carbon::now();
            $order = new Order([
                'total_amount' => 0,
                'address' => [
                    'province' => $address->province,
                    'city' => $address->city,
                    'district' => $address->district,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone
                ],
                'type' => Product::TYPE_NORMAL
            ]);
            if (!is_null($remark)) {
                $order->remark = $remark;
            }
            $order->user()->associate(Auth::id());

            $order->save();

            $total_amount = 0;
            foreach ($items as $item) {
                $sku = ProductSku::query()->find($item['sku_id']);
                $orderItem = $order->item()->make([
                    'amount' => $item['amount'],
                    'price' => $sku->price,
                ]);
                $orderItem->product()->associate($sku->product);
                $orderItem->sku()->associate($sku);
                $orderItem->save();
                $total_amount += $item['amount'] * $sku->price;
                if ($sku->decrementStock($item['amount']) <= 0) {
                    throw new \Exception('库存不足');
                }

            }

            if ($coupon) {
                $coupon->checkCouponCode($total_amount);
                $total_amount = $coupon->discount($total_amount);
                $order->couponCode()->associate($coupon);

                if ($coupon->changeUsed() <= 0) {
                    throw new CouponCodeException('优惠券已兑完');
                }
            }

            $order->update([
                'total_amount' => $total_amount
            ]);

            $ids = collect($order->item)->pluck('product_sku_id')->all();

            (new ShoppingCartService())->remove($ids);

            return $order;
        });
        dispatch(new ClosedOrder($order, config('shop.order.order_ttl')));
        return $order;
    }


    #众筹商品下单

    /**
     * @param User $user
     * @param ProductSku $sku
     * @param UserAddress $address
     * @param $amount
     * @return mixed
     * @deprecated 此功能废弃，请用 crowdfunding方法
     * @see crowdfunding()
     */
    public function crowdfundingStore(User $user, ProductSku $sku, UserAddress $address, $amount)
    {
        $order = DB::transaction(function () use ($user, $sku, $amount, $address) {
            $order = new Order([
                'extra' => ['order_type' => 'crowdfunding'],
                'total_amount' => $sku->price * $amount,
                'address' => [
                    'address' => $address->full_name,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone
                ],
                'type' => Product::TYPE_CROWDFUNDING
            ]);

            $order->user()->associate($user);
            $order->save();

            $orderItem = $order->item()->make([
                'amount' => $amount,
                'price' => $sku->price
            ]);

            $orderItem->sku()->associate($sku);
            $orderItem->product()->associate($sku->product);

            $orderItem->save();

            if ($sku->decrementStock($amount) <= 0) {
                throw new \Exception('库存不足');
            }
            return $order;
        });
        $crowdfundingTTL = $sku->product->crowdfunding->end_at->getTimestamp() - time();

        dispatch(new ClosedOrder($order, min($crowdfundingTTL, config('shop.order.order_ttl'))));

        return $order;
    }

    /**
     * 众筹订单
     * @param User $user
     * @param ProductSku $sku
     * @param UserAddress $address
     * @param $amount
     * @return mixed
     * @throws \Exception
     */
    public function crowdfunding(User $user, ProductSku $sku, UserAddress $address,$amount)
    {
        //结束时间与当前相差的秒
        $crowdfundingTTL = $sku->product->crowdfunding->end_at->getTimestamp() - time();
        //订单关闭时间不能大于设置中的订单关闭时间
        $ttl = min($crowdfundingTTL, config('shop.order.order_ttl'));

        return $this->createOrder($user, $sku, $address, $amount,Product::TYPE_CROWDFUNDING, $ttl);

    }

    /**
     * 秒杀订单
     * @param User $user
     * @param ProductSku $sku
     * @param UserAddress $address
     * @return mixed
     * @throws \Exception
     */
    public function seckill(User $user, ProductSku $sku, UserAddress $address)
    {
        //秒杀订单有效期半小时
        $ttl = config('shop.order.seckill_ttl');

        //秒杀商品只允许购买一个
        return $this->createOrder($user, $sku, $address, 1, Product::TYPE_SECKILL, $ttl);


    }

    /**
     * 创建订单核心功能
     * @param User $user 用户
     * @param ProductSku $sku 商品
     * @param UserAddress $address 收货地址
     * @param $amount int 购买数量
     * @param $type string 订单的类型，区分普通订单还是秒杀、众筹等订单
     * @param $ttl int 订单延迟关闭时间/单位秒
     * @return mixed
     * @throws \Exception
     */
    private function createOrder(User $user, ProductSku $sku, UserAddress $address, $amount, $type, $ttl)
    {
        if (!in_array($type, array_keys(Product::$typeMap))) {
            Log::error('create order error type error' . $type);
            throw new \Exception('支付错误');
        }

        $order = DB::transaction(function () use ($user, $sku, $amount, $address, $type) {
            $order = new Order([
                'extra' => ['order_type' => 'crowdfunding'],
                'total_amount' => $sku->price * $amount,
                'address' => [
                    'address' => $address->full_name,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone
                ],
                'type' => $type
            ]);

            $order->user()->associate($user);
            $order->save();

            $orderItem = $order->item()->make([
                'amount' => $amount,
                'price' => $sku->price
            ]);

            $orderItem->sku()->associate($sku);
            $orderItem->product()->associate($sku->product);

            $orderItem->save();

            if ($sku->decrementStock($amount) <= 0) {
                throw new \Exception('库存不足');
            }
            return $order;
        });

        //订单延迟关闭
        dispatch(new ClosedOrder($order, $ttl));

        return $order;
    }

    //关闭订单
    private function closeOrder($ttl)
    {

    }

    #退款功能
    public function handleRefund(Order $order)
    {
        switch ($order->payment_method) {
            case 'alipay':
                $no = Order::createRefundNo();
                $res = app('alipay')->refund([
                    'out_trade_no' => $order->no,
                    'refund_amount' => $order->total_amount,
                    'out_request_no' => $no
                ]);

                if ($res->sub_code) {
                    $extra = $order->extra ?: [];
                    $extra['alipay_refund_fail_code'] = $res->sub_code;
                    $order->update([
                        'refund_no' => $no,
                        'extra' => $extra,
                        'refund_status' => Order::REFUND_STATUS_FAILED,
                    ]);
                } else {
                    $order->update([
                        'refund_status' => Order::REFUND_STATUS_SUCCESS,
                        'refund_no' => $no
                    ]);
                }
                break;
            case 'wechat':
                //todo
                break;
            default:
                Log::error('未知支付平台');
                throw new \Exception('退款异常');
                break;
        }
    }

}
