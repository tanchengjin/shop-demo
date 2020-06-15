@extends('layouts.user.app')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                优惠券列表
                <span class="float-right"><a href="#" class="get_coupon">兑换</a></span>
            </div>
            <div class="card-body">
                @if(count($userCoupons) > 0)
                    <table class="table">
                        <thead>
                        <tr>
                            <th>标题</th>
                            <th>描述</th>
                            <th>有效期</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($userCoupons as $userCoupon)
                            <tr>
                                <td>{{$userCoupon->coupon->name}}</td>
                                <td title="{{$userCoupon->coupon->description}}" data-toggle="tooltip"
                                    data-title="{{$userCoupon->coupon->description}}">{{$userCoupon->coupon->description}}</td>
                                <td>{{$userCoupon->coupon->start_time}}至{{$userCoupon->coupon->end_time}}</td>
                                @if($userCoupon->coupon->start_time->gt(\Carbon\Carbon::now()))
                                    <td><button class="btn btn-danger">未开始</button></td>
                                    @elseif($userCoupon->coupon->end_time->lt(\Carbon\Carbon::now()))
                                    <td><button class="btn btn-danger">已过期</button></td>
                                @else
                                    <td><a href="{{route('products.index')}}" class="btn btn-primary">去使用</a></td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    暂无优惠券
                @endif
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(function () {
            $('td[data-toggle="tooltip"]').tooltip($(this).data('title'));

            $('.get_coupon').click(function(){
                swal.fire({
                    title:'兑换优惠券',
                    text:'请输入要兑换的优惠券兑换码',
                    input:'text',
                    confirmButtonText:'兑换',
                    showCancelButton:true,
                    cancelButtonText:'取消',
                    preConfirm:function(inputValue){
                        if(!inputValue){
                            swal.fire('请输入优惠券兑换码','','error');
                            return;
                        }

                        axios.post('{{route('coupon.acquire')}}',{
                            code:inputValue
                        }).then(function(res){
                            if(res){
                                swal.fire('兑换成功!','','success');
                            }else{
                                swal.fire('error','','error');
                            }
                        },function(res){
                            if(res.response.status === 403){
                                swal.fire('error',res.response.data.message,'error');
                            }else{
                                swal.fire('error','','error')
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
