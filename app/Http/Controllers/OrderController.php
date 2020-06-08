<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders=Auth::user()->order()->orderBy('created_at','desc')->paginate(16);
        return view('orders.index',compact('orders'));
    }

    public function store(OrderRequest $request,OrderService $orderService)
    {
        $remark=$request->input('remark')?:null;
        $items=$request->input('items');
        $address_id=$request->input('address_id');

        return $orderService->store($items,$address_id,$remark);
    }

    #订单确认
    public function payment(Order $order)
    {
        $order->load(['item','item.product','item.sku']);
        return view('orders.payment',compact('order'));
    }
    public function show(Order $order,Request $request)
    {
        $this->authorize('own',$order);
        return view('orders.show',compact('order'));
    }
    #收货
    public function received(Order $order)
    {
        $this->authorize('own',$order);
        if(!$order->paid_at){
            return;
        }

        $order->update([
            'ship_status'=>Order::SHIP_STATUS_RECEIVED
        ]);
        return [];
    }
    #退款
    public function refund(Order $order,Request $request)
    {
        $this->authorize('own',$order);

        if(!$order->paid_at){
            return;
        }

        $data=$this->validate($request,[
            'reason'=>['required','min:1'],
        ],[],[
            'reason'=>'退款理由'
        ]);

        $extra=$order->extra?:[];
        $extra['refund_reason']=$data['reason'];
        $order->update([
            'refund_status'=>Order::REFUND_STATUS_APPLIED,
            'extra'=>$extra
        ]);
        return [];
    }
}
