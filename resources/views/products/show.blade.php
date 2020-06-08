@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="product">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="{{$product->image}}" alt="">
                        </div>
                        <div class="col-md-8">
                            <div class="product-detail">
                                <div class="title">
                                    <span>
                                        {{$product->title}}
                                    </span>
                                </div>

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
                                    <div>
                                        <button class="btn btn-primary" id="add_to_cart">加入购物车</button>
                                    </div>
                                    <div>
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
                <ul class="nav nav-tabs">
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
                            {{$product->description}}
                        </div>
                    </div>
                    <div class="tab-pane" id="review">
                        <div class="container">
                            @if($product->review_count == 0)
                                暂无评价
                            @else
                                $product->review_count
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
    <script type="text/javascript">
        $(document).ready(function () {
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

            $('.favorite').on('click',function(){
               axios.post('{{route('products.favorite',$product->id)}}').then(function(){
                   swal.fire('success','','success');
                   location.reload();

               },function(){
                   swal.fire('error','','error');
               });
            });

            $('.disfavor').on('click',function(){
                axios.delete('{{route('products.disfavor',$product->id)}}').then(function(){
                    swal.fire('success','','success');
                    location.reload();
                },function(){
                    swal.fire('error','','error');
                });
            });

        })
    </script>
@endsection
