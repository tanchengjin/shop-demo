@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header"> </div>
        <div class="card-body text-center">
            <h1>{{$msg}}</h1>
            <a href="{{route('products.index')}}" class="btn btn-success">返回首页</a>
        </div>
    </div>
@endsection
