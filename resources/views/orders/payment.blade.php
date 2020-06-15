@extends('layouts.app')
@section('content')
    <div class="container">
        @if(count($errors) > 0)
            @foreach($errors->all() as $error)
                <div class="alert alert-danger">{{$error}}</div>
            @endforeach
        @endif
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
                        @if(!isset($userCoupons) || empty($userCoupons))
                            无
                        @else
                            @foreach($userCoupons as $userCoupon)
                                @if(!is_null($userCoupon->coupon))
                                    @if($order->total_amount > $userCoupon->coupon->min_amount)
                                        <div class="order-body-item">
                                            <input type="radio" name="coupon"
                                                   data-id="{{$userCoupon->id}}"
                                                   data-value="{{$userCoupon->coupon->value}}"
                                                   data-type="@if($userCoupon->coupon->type === 'fixed') 1 @else 2 @endif">{{$userCoupon->coupon->description}}
                                        </div>
                                    @else
                                        <div class="order-body-item">
                                            <input type="radio" name="coupon"
                                                   disabled>{{$userCoupon->coupon->description}}
                                            不可用
                                        </div>
                                    @endif
                                @endif
                            @endforeach
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
                <input type="hidden" name="c_id" value="">
                <div class="tab-content">
                    <div id="platform" class="tab-pane active container">
                        <div class="row" style="padding: 50px 0">
                            <div class="col-md-3">
                                <div class="alipay platform-active">
                                    <img
                                        src="{{asset('images/alipay.png')}}"
                                        alt="alipay">
                                    <i></i>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="wechat">
                                    <img
                                        src="{{asset('images/wechat.png')}}"
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
                        <div class="price">￥<span
                                class="total_amount_price">{{number_format($order->total_amount,2)}}</span>
                        </div>
                        <button class="btn btn-danger btn-lg payment">支付</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        //1固定，2百分比
        $(document).ready(function () {
            let url = '{{route('payment.alipay',$order)}}';
            //1 支付宝 2 微信
            let method = 1
            $('.alipay').on('click', function () {
                method = 1
                $('.wechat').removeClass('platform-active');
                $(this).addClass('platform-active');
                url = '{{route('payment.alipay',$order)}}';
                {{--                $('.payment').attr('href', '{{route('payment.alipay',$order)}}');--}}
            });
            $('.wechat').on('click', function () {
                method = 2
                $('.alipay').removeClass('platform-active');
                $(this).addClass('platform-active');
                url = '{{route('payment.wechat',$order->id)}}';
                {{--$('.payment').attr('href', '{{route('payment.wechat',$order->id)}}')--}}
            });

            $('input[type=radio]').on('change', function () {
                var id = $(this).data('id');
                $('input[name=c_id]').val(id);
                var discount = $(this).data('value')
                var type = ($(this).data('type'));
                total_amount = '{{$order->total_amount}}';
                if (type == 1) {
                    $('.total_amount_price').text(Math.floor(total_amount - discount));

                } else if (type == 2) {
                    amount = (total_amount * (100 - discount) / 100)

                    $('.total_amount_price').text(amount.toFixed(2));


                } else {
                    console.log('123')
                    return;
                }

            });

            $('.payment').click(function () {
                if (url) {
                    location.href = url + '?c_id=' + $('input[name=c_id]').val();
                }
            });
        });
    </script>
@endsection
