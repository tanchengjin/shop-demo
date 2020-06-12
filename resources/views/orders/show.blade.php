@extends('layouts.app')
@section('content')
    <div class="container">
        <nav class="breadcrumb">
            <a href="{{route('orders.index')}}" class="breadcrumb-item">我的订单</a>
            <a class="breadcrumb-item active">订单详情页</a>
        </nav>
        <div class="card">
            <div class="card-header">
                订单详情
            </div>
            <div class="card-body">
                @if(!$order->closed && is_null($order->paid_at))
                    <div class="alert alert-danger">
                        <strong>提示：</strong><span>请于 <b>{{$order->created_at->addSecond(config('shop.order.order_ttl'))->format('Y年m月d日 H:i')}}</b> 之前支付该订单</span>
                    </div>
                @endif

                <table class="table">
                    <thead>
                    <tr>
                        <th>商品信息</th>
                        <th>单价</th>
                        <th>数量</th>
                        <th class="text-right">小计</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($order->item as $item)
                        <tr>
                            <td class="product-info">
                                <div class="image">
                                    <img src="{{$item->product->full_image}}" alt="">
                                </div>
                                <div class="info">
                                    <div class="product-title">{{$item->product->title}}</div>
                                    <div class="sku-title">{{$item->sku->title}}</div>
                                </div>
                            </td>
                            <td>￥{{number_format($item->price,2)}}</td>
                            <td>{{$item->amount}}</td>
                            <td class="text-right">￥{{number_format($item->amount*$item->price,2)}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="order-bottom">
                    <div class="order-info">
                        <div class="line">
                            <div class="line-key">订单号:</div>
                            <div class="line-value">{{$order->no}}</div>
                        </div>

                        <div class="line">
                            <div class="line-key">收货地址:</div>
                            <div class="line-value">{{join(' ',$order->address)}}</div>
                        </div>

                        <div class="line">
                            <div class="line-key">订单备注:</div>
                            <div class="line-value">{{isset($order->remark)?:'-'}}</div>
                        </div>
                        @if($order->ship_status === \App\Order::SHIP_STATUS_DELIVERED)
                            <div class="line">
                                <div>物流公司</div>
                                <div>{{$order->extra['ship_company']}}</div>
                            </div>
                            <div class="line">
                                <div>物流单号</div>
                                <div>{{$order->extra['ship_no']}}</div>
                            </div>
                        @endif
                    </div>
                    <div class="order-right text-right">
                        <div class="amount">
                            <span>订单总价:</span>
                            <div class="value"><b>￥{{number_format($order->total_amount,2)}}</b></div>
                        </div>

                        <div>
                            <span>订单状态：</span>
                            <div class="value">
                                @if($order->paid_at)
                                    @if($order->refund_status === \App\Order::REFUND_STATUS_PENDING)
                                        @if(isset($order->extra['refuse_refund_reason']))
                                            卖家拒绝退款
                                        @else
                                        <span>{{\App\Order::$shipMap[$order->ship_status]}}</span>
                                        @endif
                                    @else
                                        <span>{{\App\Order::$refundMap[$order->refund_status]}}</span>
                                    @endif
                                @elseif($order->closed)
                                    订单已关闭
                                @else
                                    待付款
                                @endif
                            </div>
                        </div>

                        @if(isset($order->extra['refuse_refund_reason']))
                            <div>
                                <span>理由</span>
                                <div class="value">
                                    {{$order->extra['refuse_refund_reason']}}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
