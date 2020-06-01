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
                                <td><input type="checkbox" class="product_checkbox"
                                           @if(!$cart->sku->product->on_sale) disabled @endif></td>
                                <td>
                                    <div class="product-info {{$cart->sku->product->on_sale?'':'not_sale'}}">
                                        <div class="image">
                                            <img src="{{$cart->sku->product->image}}" alt="">
                                        </div>
                                        <div class="product-detail">
                                            <div class="product-title">
                                                <a>
                                                    {{$cart->sku->product->title}}
                                                </a>
                                            </div>
                                            <div class="sku-title">
                                                <a>
                                                    {{$cart->sku->title}}
                                                </a>
                                            </div>
                                            @if(!$cart->sku->product->on_sale)
                                                <div>
                                                    <span>该商品未上架</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>￥{{number_format($cart->sku->price,2)}} </td>
                                <td><input type="text" value="{{$cart->amount}}"
                                           class="form-control form-control-sm amount"
                                           @if(!$cart->sku->product->on_sale) disabled @endif></td>
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
                var box_status = $(this).prop('checked')

                $.each($('input[type=checkbox][class=product_checkbox]:not([disabled])'), function () {
                    $(this).prop('checked', box_status)
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
