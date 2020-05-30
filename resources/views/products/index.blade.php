@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
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
                                        <div class="title"><a href="{{route('products.show',$product->id)}}">{{$product->title}}</a></div>
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
                    {{$products->render()}}
                </div>
            </div>
        </div>
    </div>
@endsection
