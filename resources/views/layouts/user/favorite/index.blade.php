@extends('layouts.user.app')
@section('content')
    <div class="card">
        <div class="card-header">我的收藏</div>
        <div class="card-body">
            <table class="table">
                <tbody>
                @foreach($favorites as $product)
                    <tr>
                        <th>{{$product->title}}</th>
                        <th>￥{{number_format($product->min_price,2)}}</th>
                        <th><a href="{{route('products.show',$product->id)}}" class="btn btn-primary">查看</a></th>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
