@extends('layouts.user.app')
@section('content')
    <div class="card">
        <div class="card-header">
            收货地址列表
        </div>
        <div class="card-body">
            <table class="table table-bordered">
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
                        <button class="btn btn-primary">修改</button>
                        <button class="btn btn-danger">删除</button>
                    </td>
                </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
@endsection
