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
                            <tr data-id="{{$cart->sku->id}}">
                                <td><input type="checkbox" class="product_checkbox"></td>
                                <td>{{$cart->sku->title}}</td>
                                <td>{{$cart->sku->price}}</td>
                                <td><input type="text" value="{{$cart->amount}}" class="form-control form-control-sm"></td>
                                <td>
                                    <button class="btn btn-danger btn-sm btn-remove">移除</button>
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

@section('javascript')
    <script>
        $(document).ready(function () {
            $('#select-all').change(function () {
                var checkbox = $(this).prop('checked')
                $.each($('input[class=product_checkbox]:not([disabled])'), function () {
                    $(this).prop('checked', checkbox)
                });
            });

            $('.btn-remove').click(function () {
                var box = $(this).closest('tr');
                var sku_id = box.data('id');
                axios.delete('/shoppingCart/' + sku_id).then(function () {
                    box.remove();
                }, function () {
                    swal.fire('操作失败！请重试！', '', 'error');
                });


            });
        });
    </script>
@endsection
