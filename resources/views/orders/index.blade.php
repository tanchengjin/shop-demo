@extends('layouts.user.app')
@section('content')
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
                                        @if(isset($item->product))
                                            <tr>
                                                <td>
                                                    <div class="product-info">
                                                        <div class="image">
                                                            <img src="{{$item->product->full_image}}" alt="">
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
                                                    <td rowspan="{{count($order->item)}}" class="order_status">
                                                        @if($order->paid_at)
                                                            @if($order->refund_status === \App\Order::REFUND_STATUS_PENDING)
                                                                @if(isset($order->extra['refuse_refund_reason']))
                                                                    退款拒绝
                                                                @else
                                                                    已支付
                                                                @endif
                                                            @elseif($order->refund_status === \App\Order::REFUND_STATUS_APPLIED)
                                                                已发起退款请求
                                                            @elseif($order->refund_status === \App\Order::REFUND_STATUS_SUCCESS)
                                                                退款成功
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
                                                                   href="{{route('orders.payment',$order->id)}}"
                                                                   class="btn btn-success">去支付</a>
                                                            </div>
                                                        @endif

                                                        @if($order->paid_at)
                                                            @if($order->ship_status === \App\Order::SHIP_STATUS_DELIVERED)
                                                                <div style="margin-top: 10px" data-id="{{$order->id}}">
                                                                    <button class="btn btn-success received">确认收货
                                                                    </button>
                                                                </div>
                                                            @endif
                                                            @if($order->type === \App\Product::TYPE_NORMAL && $order->refund_status === \App\Order::REFUND_STATUS_PENDING)
                                                                <div style="margin-top: 10px" data-id="{{$order->id}}">
                                                                    <button class="btn btn-danger refund">发起退款</button>
                                                                </div>
                                                            @endif

                                                            @if($order->ship_status === \App\Order::SHIP_STATUS_RECEIVED)
                                                                <div style="margin-top: 10px">
                                                                    <a class="btn btn-success"
                                                                       href="{{route('orders.review.index',$order->id)}}">发起评价</a>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @else
                                            该商品已被删除
                                        @endif
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
@endsection
@section('javascript')
    <script>
        $(document).ready(function () {
            $('.received').on('click', function () {
                var id = $(this).closest('div').data('id')
                var receive = (this);
                swal.fire({
                    title: '确认要收货吗?',
                    icon: 'question',
                    showCancelButton: true,
                    cancelButtonText: '取消',
                    preConfirm(inputValue) {
                        if (inputValue) {
                            axios.post('/orders/' + id + '/received').then(function () {
                                receive.remove()
                            }, function () {
                                swal.fire('error', '', 'error')
                            });
                        }
                    }
                });
            });


            $('.refund').on('click', function () {
                var id = $(this).closest('div').data('id');
                var refund = $(this)
                swal.fire({
                    title: '请输入退款理由',
                    input: 'text',
                    preConfirm(inputValue) {
                        if (inputValue) {
                            axios.post('/orders/' + id + '/refund', {
                                reason: inputValue
                            }).then(function () {
                                refund.closest('tr').find('.order_status').text('已发起退款');
                                refund.remove()
                            }, function (res) {
                                $.each(res.response.data.errors.reason, function (key, msg) {
                                    swal.fire(msg, '', 'error')
                                });
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
