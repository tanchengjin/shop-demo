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
                                                <input type="radio" value="{{$sku->title}}" name="sku">{{$sku->title}}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="amount">
                                    <div class="form-group form-inline">
                                        <label>
                                            数量
                                            <input type="text" value="1" class="form-control form-control-sm">
                                        </label>
                                        <span id="stock"></span>
                                    </div>
                                </div>
                                <div class="buttons form-inline">
                                    <div>
                                        <button class="btn btn-primary">加入购物车</button>
                                    </div>
                                    <div>
                                        <button class="btn btn-success ">购买</button>
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
            })
        })
    </script>
@endsection
