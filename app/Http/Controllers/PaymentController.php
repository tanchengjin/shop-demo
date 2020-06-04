<?php

namespace App\Http\Controllers;

use App\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function alipay(Order $order){
        $this->authorize('own',$order);

        if($order->paid_at || $order->closed){
            return view('template.order',['msg'=>'该订单已支付或已关闭']);
        }

        $orderInfo=[
            'out_trade_no'=>$order->no,
            'total_amount'=>$order->total_amount,
            'subject'=>sprintf("支付订单%s",$order->no)
        ];
        return (app('alipay')->web($orderInfo))->send();
    }
    #支付宝前端回调
    public function alipayReturn()
    {
        try{
            $data=app('alipay')->verify();
        }catch (\Exception $e){
            return view('template.order',[
                'msg'=>'操作失败'
            ]);
        }
        return view('template.order',['msg'=>'操作成功']);
    }
    #支付宝后端回调
    public function alipayNotify()
    {
        try {
            $data=app('alipay')->verify();

            if(!in_array($data->trade_status,['TRADE_SUCCESS','TRADE_FINISHED'])){
                return 'fail';
            }

            if (!$order=Order::query()->where('no',$data->out_trade_no)->first()){
                return 'fail';
            }

            if(!$order->paid_at){
                $order->update([
                    'paid_at'=>Carbon::now(),
                    'payment_method'=>'alipay',
                    'payment_no'=>$data->trade_no
                ]);
            }
        }catch(\Exception $e){
            Log::error('alipay notify error',[$e->getMessage()]);
            return 'error';
        }
        return app('alipay')->success();
    }
}
