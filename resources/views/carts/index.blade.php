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
                                            <img src="{{$cart->sku->product->full_image}}" alt="">
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
                    <div>
                        <form>
                            <div class="form-group row">
                                <label class="col-3 text-right">收货地址</label>
                                <div class="col-7">
                                    <select name="address" id="" class="form-control">
                                        @foreach($addresses as $address)
                                        <option value="{{$address->id}}">{{$address->address}}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-right">备注:</label>
                                <div class="col-md-7 col-sm-9">
                                    <textarea name="remark" rows="3" style="resize: none" class="form-control"></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="offset-3 col-sm-3">
                                    <button type="button" class="create_order btn btn-primary">提交订单</button>
                                </div>
                            </div>
                        </form>
                    </div>
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
                swal.fire({
                    title:'确定要移除吗？',
                    text:'移除后将不可恢复',
                    icon:'warning',
                    showConfirmButton:true,
                    showCancelButton:true,
                    confirmButtonText:'确定',
                    cancelButtonText:'取消',
                    preConfirm(inputValue) {
                        if(inputValue){
                            axios.delete('/shoppingCart/' + sku_id).then(function () {
                                box.remove();
                            }, function () {
                                swal.fire('操作失败！请重试！', '', 'error');
                            });
                        }
                    }

                });
            });


            $('.create_order').click(function(){
               var res={
                   remark:$('textarea[name=remark]').val(),
                   address_id:$('select option:selected').val(),
                   items:[],
               };

               $('table tr[data-id]').each(function(){
                   $checkbox=$(this).find('input[type=checkbox][class=product_checkbox]:not([disabled])')
                   if(!$checkbox.prop('checked') || $checkbox.prop('disabled')){
                       return;
                   }
                   var amount=$(this).find('.amount').val()

                   if(amount === 0 || isNaN(amount)){
                       return;
                   }
                   res.items.push({
                      'sku_id':$(this).data('id'),
                      'amount':amount
                   });
               });

               axios.post('{{route('order.store')}}',res).then(function(res){
                   location.href='{{route('orders.index')}}';
               },function(){
                   swal.fire('error','','error')
                });
            });
        });
    </script>
@endsection
