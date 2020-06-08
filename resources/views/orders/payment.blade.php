@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <span>订单支付</span>
                <a class="float-right" href="{{route('orders.index')}}">返回订单列表</a>
            </div>
            <div class="card-body">
                <div class="alert alert-danger">
                    请于{{$order->created_at->addSecond(config('shop.order.order_ttl'))}}之前支付该订单
                </div>

                <div class="order">
                    <div class="order-header">
                        订单信息
                    </div>
                    <div class="order-body">
                        <div class="order-body-item">
                            <a href="{{route('orders.show',$order->id)}}" target="_blank">{{$order->no}}</a>
                            <span class="float-right">应付金额: ￥{{number_format($order->total_amount,2)}}</span>
                        </div>
                    </div>
                </div>

                <div class="order">
                    <div class="order-header">
                        优惠券
                    </div>
                    <div class="order-body">
                        @if(!isset($coupon) || empty($coupon))
                            无
                        @else
                            <div class="order-body-item">
                                <input type="radio" name="coupon">
                            </div>
                            <div class="order-body-item">
                                <input type="radio" name="coupon">
                            </div>
                        @endif
                    </div>
                </div>

                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a data-toggle="tab" class="nav-link active" href="#platform">支付平台支付</a>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="tab" class="nav-link" href="#other">其他支付方式</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div id="platform" class="tab-pane active container">
                        <div class="row" style="padding: 50px 0">
                            <div class="col-md-3">
                                <div class="alipay platform-active">
                                    <img
                                        src="https://finance.huawei.com/cashier/web/images/serverIcon/2x/ALIPAY.png?v=20200324"
                                        alt="alipay">
                                    <i></i>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="wechat">
                                    <img
                                        src="https://finance.huawei.com/cashier/web/images/serverIcon/2x/WXPAY.png?v=20200324"
                                        alt="wechat">
                                    <i></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="other" class="container tab-pane ">

                    </div>
                </div>

                <div class="payment_group">
                    <div class="float-right">
                        <div class="price">￥{{number_format($order->total_amount,2)}}</div>
                        <a href="{{route('payment.alipay',$order->id)}}" class="btn btn-danger btn-lg payment">支付</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function () {
            //1 支付宝 2 微信
            let method = 1
            $('.alipay').on('click', function () {
                method = 1
                $('.wechat').removeClass('platform-active');
                $(this).addClass('platform-active');
                $('.payment').attr('href','{{route('payment.alipay',$order)}}')
            });
            $('.wechat').on('click', function () {
                method = 2
                $('.alipay').removeClass('platform-active');
                $(this).addClass('platform-active');
                $('.payment').attr('href','#')
            });
        });
    </script>
@endsection
