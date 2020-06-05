@extends('layouts.user.app')
@section('content')
    <div class="card" id="app">
        <div class="card-header">
            收货地址管理
        </div>
        <div class="card-body">
            @if(count($errors)>0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <user_address_add_and_edit inline-template>
                @if(isset($address))
                    <form action="{{route('user.addresses.update',$address->id)}}" method="post">
                        {{method_field('PUT')}}
                    @else
                    <form action="{{route('user.addresses.store')}}" method="post">
                        @endif
                        {{csrf_field()}}

                        <user_address @change="onDistrictChanged" inline-template>
                            <div class="form-group row">
                                <div class="col-md-2 text-md-right col-form-label">
                                    <label for="">请选择地址</label>
                                </div>
                                <div class="col-md-3">
                                    <select name="" id="" class="form-control" v-model="provinceId">
                                        <option value="">请选择省--</option>
                                        <option v-for="(name,id) in provinces" :value="id">@{{ name }}</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <select name="" id="" class="form-control" v-model="cityId">
                                        <option value="">--请选择市--</option>
                                        <option v-for="(name,id) in cities" :value="id">@{{ name }}</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <select name="" id="" class="form-control" v-model="districtId">
                                        <option value="">--请选择区--</option>
                                        <option v-for="(name,id) in districts" :value="id">@{{ name }}</option>
                                    </select>
                                </div>
                            </div>
                        </user_address>
                        <div class="form-group row">
                            <div class="col-md-2 text-md-right col-form-label">
                                <label for="">收货地址</label>
                            </div>
                            <div class="col-md-9">
                                <input name="address" type="text" class="form-control"
                                       value="{{old('address',isset($address)?$address->address:'')}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2 text-md-right col-form-label">
                                <label for="">邮编</label>
                            </div>
                            <div class="col-md-9">
                                <input name="zip" type="text" class="form-control"
                                       value="{{old('zip',isset($address)?$address->zip:'')}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2 text-md-right col-form-label">
                                <label for="">联系人</label>
                            </div>
                            <div class="col-md-9">
                                <input name="contact_name" type="text" class="form-control"
                                       value="{{old('contact_name',isset($address)?$address->contact_name:'')}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2 text-md-right col-form-label">
                                <label for="">联系电话</label>
                            </div>
                            <div class="col-md-9">
                                <input name="contact_phone" type="text" class="form-control"
                                       value="{{old('contact_phone',isset($address)?$address->contact_name:'')}}">
                            </div>
                        </div>

                        <div class="form-group text-center">
                            <button class="btn btn-primary">提交</button>
                        </div>
                        <input type="hidden" name="province" v-model="province">
                        <input type="hidden" name="city" v-model="city">
                        <input type="hidden" v-model="district" name="district">
                    </form>
            </user_address_add_and_edit>
        </div>
    </div>
@endsection
