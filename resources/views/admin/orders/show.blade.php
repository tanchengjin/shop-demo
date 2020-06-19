<div class="box box-info">
    <div class="box-header">
        <div class="box-title">订单号{{$order->no}}</div>
        <div class="box-tools">
            <div class="btn-group pull-right" style="margin-right: 5px">
                <a href="http://shop.test/admin/orders" class="btn btn-sm btn-default" title="列表">
                    <i class="fa fa-list"></i><span class="hidden-xs"> 列表</span>
                </a>
            </div>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered">
            <tbody>
            <tr>
                <td>购买人</td>
                <td>{{$order->user->name}}</td>
                <td>付款时间</td>
                <td>{{$order->paid_at?:'未付款'}}</td>
            </tr>
            @if($order->paid_at)
                <tr>
                    <td>支付方式</td>
                    <td>{{\App\Order::$paymentMap[$order->payment_method]}}</td>
                    <td>支付单号</td>
                    <td>{{$order->payment_no}}</td>
                </tr>
            @endif
            <tr>
                <td>收货地址</td>
                <td colspan="3">{{join(' ',$order->address)}}</td>
            </tr>
            <tr>
                <td rowspan="{{count($order->item)+1}}">商品信息</td>
                <td>商品名</td>
                <td>单价</td>
                <td>数量</td>
            </tr>
            @foreach($order->item as $item)
                <tr>
                    <td>{{$item->sku->title}}</td>
                    <td>{{$item->price}}</td>
                    <td>{{$item->amount}}</td>
                </tr>
            @endforeach
            <tr>
                <td>订单金额</td>
                <td colspan="3">￥{{$order->total_amount}}</td>
            </tr>
            <tr>
                <td>订单备注</td>
                <td>{{$order->remark?:'无'}}</td>
                <td>物流状态</td>
                <td>{{\App\Order::$shipMap[$order->ship_status]}}</td>
            </tr>
            @if($order->paid_at)
                @if($order->ship_status == \App\Order::SHIP_STATUS_PENDING)
                    @if($order->type === \App\Product::TYPE_NORMAL || ($order->type === \App\Product::TYPE_CROWDFUNDING && $order->item[0]->product->crowdfunding->status === \App\CrowdfundingProduct::STATUS_SUCCESS))
                        <tr>
                            <td colspan="4">
                                <form action="{{route('admin.orders.ship',$order->id)}}" class="form-inline"
                                      method="post">
                                    {{csrf_field()}}
                                    <div class="form-group @if($errors->has('ship_company')) has-error @endif">
                                        <label for="" class="col-form-label">物流公司</label>
                                        <input type="text" name="ship_company" class="form-control">
                                        @if($errors->has('ship_company'))
                                            @foreach($errors->get('ship_company') as $msg)
                                                <span class="help-block">{{$msg}}</span>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="form-group @if($errors->has('ship_no')) has-error @endif">
                                        <label for="" class="col-form-label">物流单号</label>
                                        <input type="text" name="ship_no" class="form-control">
                                        @if($errors->has('ship_no'))
                                            @foreach($errors->get('ship_no') as $msg)
                                                <span class="help-block">{{$msg}}</span>
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <button class="btn btn-primary">发货</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endif
                @else
                    <tr>
                        <td>物流公司</td>
                        <td>{{$order->extra['ship_company']}}</td>
                        <td>物流单号</td>
                        <td>{{$order->extra['ship_no']}}</td>
                    </tr>
                @endif


                @if($order->refund_status === \App\Order::REFUND_STATUS_APPLIED)
                    <tr>
                        <td>买家已发起退款请求</td>
                        <td>理由</td>
                        <td>{{$order->extra['refund_reason']}}</td>
                        <td>
                            <button class="btn btn-success refund_agree">同意</button>
                            <button class="btn btn-danger refund_reject">拒绝</button>
                        </td>
                    </tr>
                @endif
            @endif
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.refund_reject').on('click', function () {
            swal.fire({
                title: '请输入拒绝理由',
                input: 'text',
                preConfirm: function (value) {
                    if (value) {
                        $.ajax({
                            url: '{{route('admin.orders.refund',$order->id)}}',
                            method: 'post',
                            data: JSON.stringify({
                                agree: false,
                                reason: value,
                                '_token': '{{csrf_token()}}'
                            }),
                            contentType: 'application/json',
                            success: function (res) {
                                location.reload()
                            },
                            error: function () {
                                swal.fire('error', '', 'error')
                            },
                        });
                    }
                }
            });
        });

        $('.refund_agree').on('click', function () {
            $.ajax({
                url: '{{route('admin.orders.refund',$order->id)}}',
                method: 'post',
                data: JSON.stringify({
                    agree: true,
                    '_token': '{{csrf_token()}}'
                }),
                contentType: 'application/json',
                success: function (res) {
                    location.reload()
                },
                error: function () {
                    swal.fire('error', '', 'error')
                },
            });
        })
    });
</script>
