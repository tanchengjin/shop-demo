@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <div class="list-group">
                    <a href="#" class="list-group-item">收货地址</a>
                    <a href="#" class="list-group-item active">我的订单</a>
                    <a href="#" class="list-group-item">3</a>
                </div>
            </div>
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">我的订单</div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach($orders as $order)
                                <li class="list-group-item">
                                    <div class="card">
                                        <div class="card-header">
                                            <span>订单号：{{$order->no}}</span>
                                            <span class="float-right">创建时间：{{$order->created_at}}</span>
                                        </div>
                                        <div class="card-body">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>商品信息</th>
                                                    <th>单价</th>
                                                    <th>数量</th>
                                                    <th>订单状态</th>
                                                    <th>订单价格</th>
                                                    <th>操作</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($order->item as $index=>$item)
                                                    <tr>
                                                        <td>
                                                            <div class="product-info">
                                                                <div class="image">
                                                                    <img src="{{$item->product->image}}" alt="">
                                                                </div>
                                                                <div id="info">
                                                                    <div class="product-title">
                                                                        <div>
                                                                            {{$item->product->title}}
                                                                        </div>
                                                                    </div>
                                                                    <div class="sku-title">
                                                                        <div>{{$item->sku->title}}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>￥{{number_format($item->price,2)}}</td>
                                                        <td>{{$item->amount}}</td>
                                                        @if($index === 0)
                                                            <td rowspan="{{count($order->item)}}">
                                                                @if($order->paid_at)
                                                                    @if($order->refund_status === \App\Order::$refundMap[\App\Order::REFUND_STATUS_PENDING])
                                                                        已支付
                                                                    @else
                                                                        {{\App\Order::$refundMap[$order->refund_status]}}
                                                                    @endif
                                                                @elseif($order->closed)
                                                                    订单已关闭
                                                                @else
                                                                    <div class="text-center">
                                                                        请于{{$order->created_at->addSecond(config('shop.order.order_ttl'))->format('Y-m-d H:i')}}
                                                                        之前支付
                                                                    </div>
                                                                @endif
                                                            </td>

                                                            <td rowspan="{{count($order->item)}}">
                                                                <b>￥{{number_format($order->total_amount,2)}}</b>
                                                            </td>
                                                            <td rowspan="{{count($order->item)}}" class="text-center">
                                                                <a href="{{route('orders.show',$order->id)}}"
                                                                   class="btn btn-primary">查看订单</a>
                                                                @if(!$order->paid_at && !$order->closed)
                                                                    <div style="margin-top: 10px">
                                                                        <a target="_blank"
                                                                           href="{{route('payment.alipay',$order->id)}}"
                                                                           class="btn btn-success">去支付</a>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="float-right">
                            {{$orders->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
