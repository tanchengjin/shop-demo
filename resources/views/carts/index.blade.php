@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                @if(isset($carts) && count($carts) > 0)
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>商品信息</th>
                        <th>单价</th>
                        <th>数量</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($carts as $cart)
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>1</td>
                            <td>{{$cart->sku->price}}</td>
                            <td>{{$cart->amount}}</td>
                            <td>
                                <button class="btn btn-danger btn-sm">移除</button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @else
                    购物车内还没有任何商品，快去挑选喜欢的商品吧。
                    @endif
            </div>
        </div>
    </div>
@endsection
