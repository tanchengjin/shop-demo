@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="product">
                    <div class="row">
                        <div class="col-md-4 product-image">
                            <img src="{{$product->full_image}}" alt="">
                        </div>
                        <div class="col-md-8">
                            <div class="product-detail">
                                <div class="title">
                                    <span>
                                        {{$product->title}}
                                    </span>
                                </div>
                                @if($product->type === \App\Product::TYPE_CROWDFUNDING)
                                    <div class="crowdfunding-info">
                                        <div>已筹到</div>
                                        <div class="total_amount">
                                            <span>￥</span>{{$product->crowdfunding->current_amount}}</div>

                                        <div class="progress">
                                            <div class="progress-bar progress-bar-success progress-bar-striped"
                                                 role="progressbar" aria-valuenow="30" aria-valuemin="0"
                                                 aria-valuemax="100"
                                                 style="width: {{$product->crowdfunding->percent}}%">

                                            </div>
                                        </div>
                                        <div class="progress-info">
                                            <span
                                                class="current-progress">当前进度: {{$product->crowdfunding->percent}}%</span>
                                            <span class="float-right user_count">{{$product->crowdfunding->user_count}}名支持者</span>
                                        </div>

                                        @if($product->crowdfunding->status === \App\CrowdfundingProduct::STATUS_FUNDING)
                                            <div>此商品必须在
                                                <span
                                                    class="text-red">{{$product->crowdfunding->end_at->format('Y-m-d')}}</span>
                                                前众筹到
                                                <span
                                                    class="text-red">{{number_format($product->crowdfunding->target_amount)}}</span>为成功

                                                众筹将在 <span
                                                    class="text-red">{{$product->crowdfunding->end_at->diffForHumans(now())}}</span>结束
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                <div class="price">
                                    <label for="">价格</label>
                                    <span>
                        ￥{{$product->min_price}}
                    </span>
                                </div>
                                <div class="sold_and_review">
                                    <div class="sold_count">
                                        累计销量 <span>{{$product->sold_count}}</span>
                                    </div>
                                    <div class="review_count">
                                        累计评价 <span>{{$product->review_count}}</span>
                                    </div>
                                </div>
                                <div class="skus">
                                    <div class="form-inline">
                                        <span>规格</span>

                                        @foreach($product->sku as $sku)
                                            <label data-price="{{$sku->price}}" data-stock="{{$sku->stock}}"
                                                   class="sku">
                                                <input type="radio" value="{{$sku->id}}" name="sku">{{$sku->title}}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="amount">
                                    <div class="form-group form-inline">
                                        <label>
                                            数量
                                            <input type="text" value="1" class="form-control form-control-sm"
                                                   name="amount">
                                        </label>
                                        <span id="stock"></span>
                                    </div>
                                </div>
                                <div class="buttons form-inline">

                                    @if($product->type === \App\Product::TYPE_CROWDFUNDING)
                                        @if(\Illuminate\Support\Facades\Auth::check())
                                            @if($product->crowdfunding->status === \App\CrowdfundingProduct::STATUS_FUNDING)
                                                <button class="btn btn-primary btn-crowdfunding"
                                                        style="margin-right: 10px">众筹
                                                </button>
                                            @else
                                                <button
                                                    class="btn btn-primary disabled"
                                                    style="margin-right: 5px">{{\App\CrowdfundingProduct::$statusMap[$product->crowdfunding->status]}}</button>
                                            @endif
                                        @else
                                            <a class="btn btn-primary" href="{{route('login')}}">登录</a>
                                        @endif
                                    @elseif($product->type === \App\Product::TYPE_SECKILL)
                                        @if(\Illuminate\Support\Facades\Auth::check())
                                            @if($product->seckill->is_before_start)
                                                <button class="btn btn-primary disabled btn-seckill">抢购倒计时</button>
                                            @elseif($product->seckill->is_after_end)
                                                <button class="btn btn-primary disabled btn-seckill">抢购已结束</button>
                                            @else
                                                <button class="btn btn-primary btn-seckill">立即抢购</button>
                                            @endif
                                        @else
                                            <a class="btn btn-primary" href="{{route('login')}}"
                                               style="margin-right: 5px">请先登录</a>
                                        @endif
                                    @else
                                        <div>
                                            <button class="btn btn-primary" id="add_to_cart">加入购物车</button>
                                        </div>
                                        <div>
                                            @endif

                                            @if(!$favorite)
                                                <button class="btn btn-success favorite">收藏</button>
                                            @else
                                                <button class="btn btn-danger disfavor">取消收藏</button>
                                            @endif
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="nav nav-tabs" style="margin-top: 30px">
                    <li class="nav-item">
                        <a href="#description" data-toggle="tab" class="nav-link active">商品详情</a>
                    </li>
                    <li class="nav-item">
                        <a href="#review" class="nav-link" data-toggle="tab">评价</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="description">
                        <div class="container">
                            <div class="product-properties">
                                <div class="product-properties-title">产品参数</div>
                                <ul class="product-properties-list">
                                    @foreach($product->properties as $property)
                                        <li>{{$property->name}}：{{$property->value}}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="product-description m-auto">
                                {!! $product->description !!}
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="review">
                        <div class="container">
                            @if($product->review_count == 0)
                                暂无评价
                            @else
                                @foreach($reviews as $review)
                                    <table class="table table-bordered table-striped" style="margin-top: 10px">
                                        <tbody>
                                        <tr>
                                            <th>用户</th>
                                            <th>商品</th>
                                            <th>评价内容</th>
                                            <th>评分</th>
                                            <th>评论时间</th>
                                        </tr>
                                        <tr>
                                            <td>{{$review->order->user->name}}</td>
                                            <td>{{$review->product->title}}</td>
                                            <td>{{$review->review}}</td>
                                            <td>{{$review->rating}}</td>
                                            <td>{{date('Y-m-d',strtotime($review->reviewed_at))}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>
@endsection
@section('javascript')

    @if($product->type === \App\Product::TYPE_SECKILL && $product->seckill->is_before_start)
        <script src="https://cdn.bootcss.com/moment.js/2.22.1/moment.min.js"></script>
    @endif
    <script type="text/javascript">
        $(document).ready(function () {
            $('.btn-seckill').click(function () {
                var sku = $('input[name=sku]:checked').val();
                if (!sku) {
                    swal.fire('请选择商品', '', 'error');
                    return;
                }

                var addresses ={!! json_encode(\Illuminate\Support\Facades\Auth::check()?auth()->user()->addresses->toArray():[]) !!};

                var addressesBox = $('<select name="address_id"></select>');

                addresses.forEach(function (address) {
                    addressesBox.append("<option value=" + address.id + ">" + address.full_address + '' + address.contact_name + '' + address.contact_phone
                        + "</option>"
                    )
                });
                console.log(addressesBox[0])
                swal.fire({
                    title: '请选择收货地址',
                    icon:'info',
                    html: addressesBox[0]
                }).then(function (result) {
                    if (!result) {
                        return;
                    } else {
                        address=addressesBox.val();

                        if(!address){
                            return;
                        }
                        var res = {
                            'sku_id': sku,
                            'address_id': address
                        };

                        console.log(res)
                        axios.post('{{route('order.seckill')}}', res).then(function (res) {
                            swal.fire('操作成功', '', 'success');
                            location.href='{{route('orders.index')}}';
                        }, function (res) {
                            console.log(res);
                            if (res.response.status === 422) {
                                var e_html = '<div>';
                                _.each(res.response.data.errors, function (errors) {
                                    _.each(errors, function (error) {
                                        e_html += error + '</br>';
                                    });
                                });
                                e_html += '</div>';
                                swal.fire(e_html, '', 'error')
                            }
                        });
                    }
                })
            });

                @if($product->type === \App\Product::TYPE_SECKILL && $product->seckill->is_before_start)
            var startTime = moment.unix({{$product->seckill->start_at->getTimestamp()}});

            var hd1 = setInterval(function () {
                var now = moment();

                if (now.isAfter(startTime)) {
                    $('.btn-seckill').remove('disabled').removeClass('countdown').text('立即抢购');
                    clearInterval(hd1);
                    return;
                }

                var hour = startTime.diff(now, 'hours');
                var minute = startTime.diff(now, 'minutes') % 60;
                var seconds = startTime.diff(now, 'seconds') % 60;
                $('.btn-seckill').text('抢购倒计时' + hour + ':' + minute + ':' + seconds);
            }, 500);
            @endif


            $('.btn-crowdfunding').click(function () {
                var sku = $('input[name=sku]:checked').val()

                if (!sku) {
                    swal.fire('请选择商品规格!');
                    return;
                }

                var addresses =
                    {!! json_encode(\Illuminate\Support\Facades\Auth::check()?\Illuminate\Support\Facades\Auth::user()->addresses:[]) !!}


                var $form = $("<form method='post' class='form-horizontal'></form>")

                $form.append("<div class='form-group form-inline'>" +
                    "<label class='control-label col-sm-3'>收货地址</label>" +
                    "<div class='col-sm-9'>" +
                    "<select class='form-control' name='address_id'></select>" +
                    "</div>" +
                    "</div>"
                )
                ;

                addresses.forEach(function (address) {
                    $form.find("select[name=address_id]").append(
                        "<option value=" + address.id + ">" + address.full_address + '' + address.contact_name + address.contact_phone + "</option>");
                });

                swal.fire({
                    title: '请选择收货地址',
                    html: $form[0],
                    preConfirm(inputValue) {
                        if (!inputValue) {
                            return;
                        }
                        var res = {
                            'sku_id': sku,
                            'amount': $('input[name=amount]').val(),
                            'address_id': $form.find("select[name=address_id]").val()
                        };

                        if (res) {
                            axios.post('{{route('order.crowdfunding')}}', res).then(function (res) {
                                swal.fire('众筹成功', '', 'success').then(function () {
                                    location.href = '{{route('orders.index')}}';
                                });
                            }, function (res) {
                                if (res.response.status === 422) {
                                    console.log(res.response)
                                    var html = '<div>';
                                    _.each(res.response.data.errors, function (errors) {
                                        _.each(errors, function (error) {
                                            html += error + "</br>";
                                        });
                                    });
                                    html += '<div>';
                                    swal.fire(html, '', 'error');
                                } else {
                                    swal.fire('error', '服务器内部错误', 'error');
                                }
                            });
                        }
                    }
                })
            });

            $('.sku').click(function () {
                $('.price span').text('￥' + $(this).data('price'))
                $('#stock').text('库存 ' + $(this).data('stock'))
            });

            $('#add_to_cart').click(function () {
                amount = $('input[name=amount]').val()
                sku = $('input[name=sku]:checked').val();

                if (!sku) {
                    swal.fire('请选择商品规格', '', 'warning');
                    return;
                }

                axios.post('{{route('carts.store')}}', {
                    'id': sku,
                    'amount': amount
                }).then(function (res) {
                    swal.fire({
                        'title': '操作成功!',
                        'text': '是否现在去支付',
                        icon: 'success',
                        showCancelButton: true,
                        showConfirmButton: true,
                        cancelButtonText: '留在本页面',
                        confirmButtonText: '去支付',
                        preConfirm(inputValue) {
                            location.href = '{{route('carts.index')}}';
                        }
                    });
                }, function (res) {
                    if (res.response.status === 401) {
                        swal.fire('error', '请先完成登录', 'warning').then(function () {
                            location.href = '{{route('login')}}';
                        })
                        return;
                    }
                    swal.fire('error', '服务器错误', 'error')
                })
            });

            $('.favorite').on('click', function () {
                axios.post('{{route('products.favorite',$product->id)}}').then(function () {
                    swal.fire('success', '', 'success');
                    location.reload();

                }, function () {
                    swal.fire('error', '', 'error');
                });
            });

            $('.disfavor').on('click', function () {
                axios.delete('{{route('products.disfavor',$product->id)}}').then(function () {
                    swal.fire('success', '', 'success');
                    location.reload();
                }, function () {
                    swal.fire('error', '', 'error');
                });
            });

        })
    </script>
@endsection
