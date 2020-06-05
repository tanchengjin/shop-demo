@extends('layouts.user.app')
@section('content')
    <div class="card">
        <div class="card-header">
            收货地址列表
        </div>
        <div class="card-body">
            <a class="btn btn-outline-primary" href="{{route('user.addresses.create')}}"
               style="margin-bottom: 10px">新增</a>
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>收货人</th>
                    <th>地址</th>
                    <th>邮编</th>
                    <th>联系电话</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($addresses as $address)
                    <tr>
                        <td>{{$address->contact_name}}</td>
                        <td>{{$address->address}}</td>
                        <td>{{$address->zip}}</td>
                        <td>{{$address->contact_phone}}</td>
                        <td>
                            <a href="{{route('user.addresses.edit',$address->id)}}" class="btn btn-primary">修改</a>
                            <button class="btn btn-danger btn-delete" type="button" data-id="{{$address->id}}">删除
                            </button>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function () {
            $(function () {
                $('.btn-delete').click(function () {
                    swal.fire({
                        title: '确定要删除吗？',
                        text: '该操作不可恢复',
                        icon: 'question',
                        preConfirm(inputValue) {
                            if (inputValue) {
                                var id = $('.btn-delete').data('id');

                                if (!id) {
                                    return;
                                }
                                axios.delete('/center/address/' + id)
                                    .then(function () {
                                        $('.btn-delete').closest('tr').remove();
                                    }, function () {
                                        swal.fire('操作失败请重试!', '', 'error')
                                    });
                            }
                        },
                        showCancelButton: true,
                        cancelButtonText: '取消',
                        confirmButtonText: '确定'
                    });
                });
            });
        });
    </script>
@endsection
