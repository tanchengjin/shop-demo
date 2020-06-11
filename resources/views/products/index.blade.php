@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <form action="{{route('products.index')}}" class="form-search">
                    <div class="form-row">
                        <div class="col-md-9">
                            <div class="form-row">
                                <div class="col-auto">
                                    <input type="text" name="q" class="form-control">

                                </div>
                                <button class="btn btn-primary">搜索</button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="order" id="" class="form-control order">
                                <option value="">排序方式</option>
                                <option value="price_asc">价格升序</option>
                                <option value="price_desc">价格降序</option>
                                <option value="sold_asc">销量升序</option>
                                <option value="sold_desc">销量降序</option>
                                <option value="review_asc">评价升序</option>
                                <option value="review_desc">评价降序</option>
                            </select>
                        </div>
                    </div>
                </form>


                <div class="products-content">
                    <div class="row">
                        @foreach($products as $product)
                            <div class="col-md-3 col-sm-6">
                                <div class="product-item">
                                    <div class="top">
                                        <div>
                                            <a href="{{route('products.show',$product->id)}}">
                                                <img src="{{$product->image}}" alt="{{$product->title}}">
                                            </a>
                                        </div>
                                        <div class="price">￥{{$product->min_price}}</div>
                                        <div class="title"><a
                                                href="{{route('products.show',$product->id)}}">{{$product->title}}</a>
                                        </div>
                                    </div>
                                    <div class="bottom">
                                        <div class="sold_count">销量：<span>{{$product->sold_count}}</span></div>
                                        <div class="review_count">评价：<span>{{$product->review_count}}</span></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="float-right">
                    {{$products->appends($data)->render()}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function () {
            $('.order').on('change', function () {
                $('.form-search').submit();
            });

            $('select[name=order]').val('{{$data['order']}}');
            $('input[name=q]').val('{{$data['search']}}');
        });
    </script>
@endsection
