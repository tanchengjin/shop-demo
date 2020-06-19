<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Order;
use App\Product;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function alipay(Order $order)
    {
        $this->authorize('own', $order);

        if ($order->paid_at || $order->closed) {
            return view('template.order', ['msg' => '该订单已支付或已关闭']);
        }
        //普通订单可以使用优惠券
        if ($order->type === Product::TYPE_NORMAL) {
            #判断优惠券
            $total_amount = $this->checkCouponCode($order);
        } else {
            $total_amount = $order->total_amount;

        }


        $orderInfo = [
            'out_trade_no' => $order->no,
            'total_amount' => $total_amount,
            'subject' => sprintf("支付订单%s", $order->no)
        ];

        return (app('alipay')->web($orderInfo))->send();
    }

    #支付宝前端回调
    public function alipayReturn()
    {
        try {
            $data = app('alipay')->verify();
        } catch (\Exception $e) {
            return view('template.order', [
                'msg' => '操作失败'
            ]);
        }
        return view('template.order', ['msg' => '操作成功']);
    }

    #支付宝后端回调
    public function alipayNotify()
    {
        try {
            $data = app('alipay')->verify();

            if (!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
                return 'fail';
            }

            if (!$order = Order::where('no', $data->out_trade_no)->first()) {
                return 'fail';
            }


            if (!$order->paid_at) {
                $extra = $order->extra ?: [];
                $extra = [
                    'paid_amount' => $data->total_amount
                ];
                $order->update([
                    'paid_at' => Carbon::now(),
                    'payment_method' => 'alipay',
                    'payment_no' => $data->trade_no,
                    'extra' => $extra
                ]);


                $this->soldCount($order);

                if (!is_null($order->coupon_code_id) || $order->userCoupon()->first()){
                    $order->userCoupon->delete();
                }

            }
        } catch (\Exception $e) {
            Log::error('alipay notify error', [$e->getMessage()]);
            return 'error';
        }
        return app('alipay')->success();
    }

    public function soldCount(Order $order)
    {
        event(new OrderPaid($order));
    }

    public function wechat(Order $order)
    {
        $this->authorize('own', $order);

        if ($order->paid_at || $order->closed) {
            return view('template.order', ['msg' => '该订单已支付或已关闭']);
        }

        #判断优惠券
        #判断优惠券
        $total_amount = $this->checkCouponCode($order);

        $orderInfo = [
            'out_trade_no' => $order->no,
            'total_fee' => $order->total_amount,
            'body' => '支付订单-' . $order->no,
            'openid' => ''
        ];
        $data = app('wechat')->scan($orderInfo);

        $qrcode = new QrCode($data->code_url);

        return response($qrcode->writeString(), 200, [
            'Content-Type' => $qrcode->getContentType()
        ]);
        // $pay->appId
        // $pay->timeStamp
        // $pay->nonceStr
        // $pay->package
        // $pay->signType
    }

    public function wechatNotify()
    {

        try {
            $data = app('wechat')->verify();

            if (!$order = Order::where('no', $data->out_trade_no)->first()) {
                return 'fail';
            }
            if (!$order->paid_at) {

                $order->update([
                    'payment_method' => Order::WECHATPAY,
                    'paid_at' => Carbon::now(),
                    'payment_no' => $data->transaction_id
                ]);
                $this->soldCount($order);
                $userCoupon = $order->couponCode->userCoupon()->first();
                $userCoupon->update([
                    'used_at' => Carbon::now()
                ]);
            }


        } catch (\Exception $e) {
            // $e->getMessage();
        }
        return app('wechat')->success();

    }

    public function checkCouponCode(Order $order)
    {
        if ($user_coupon_id = \request()->input('c_id')) {
            #verify
            if ($UserCoupon = Auth::user()->userCoupon()->find($user_coupon_id)) {
                if ($UserCoupon->user_id !== Auth::user()->id) {
                    throw new \Exception('unknown coupon ');
                }
            } else {
                throw new \Exception('invalid coupon code');
            }


            $UserCoupon->checkAvailable();


            $order->update([
                'user_coupon_code_id' => $UserCoupon->id,
            ]);
            $coupon = $UserCoupon->coupon;

            $total_amount = $coupon->discount($order->total_amount, false);
            $UserCoupon->checkAvailable($total_amount);
        } else {
            #not coupon
            $total_amount = $order->total_amount;
        }
        return round($total_amount, 2);

    }
}
