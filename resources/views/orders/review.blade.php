@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <span>订单评价</span>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>商品信息</th>
                        <th>评分</th>
                        <th>评价</th>
                    </tr>
                    </thead>
                    <form action="{{route('orders.review.store',$order->id)}}" method="POST">

                        <tbody>
                        @foreach($order->item as $index=>$item)
                            <tr>
                                <th>{{$item->product->title}}</th>
                                {{csrf_field()}}
                                <th>
                                    @if($order->reviewed)
                                        <span class="rating-start-yes">{{str_repeat('❤',$item->rating)}}</span>
{{--                                        <span class="rating-start-no">{{str_repeat('❤',$item->rating)}}</span>--}}
                                    @else
                                        <ul class="rate-area">
                                            <input type="radio" id="5-star-{{$index}}"
                                                   name="reviews[{{$index}}][rating]" value="5" checked>
                                            <label for="5-star-{{$index}}">五星</label>

                                            <input type="radio" id="4-star-{{$index}}"
                                                   name="reviews[{{$index}}][rating]" value="4">
                                            <label for="4-star-{{$index}}">四星</label>

                                            <input type="radio" id="3-star-{{$index}}"
                                                   name="reviews[{{$index}}][rating]" value="3">
                                            <label for="3-star-{{$index}}">三星</label>

                                            <input type="radio" id="2-star-{{$index}}"
                                                   name="reviews[{{$index}}][rating]" value="2">
                                            <label for="2-star-{{$index}}">二星</label>

                                            <input type="radio" id="1-star-{{$index}}"
                                                   name="reviews[{{$index}}][rating]" value="1">
                                            <label for="1-star-{{$index}}">一星</label>
                                        </ul>
                                    @endif
                                </th>
                                <input type="hidden" name="reviews[{{$index}}][id]" value="{{$item->id}}">
                                <th>
                                    @if($order->reviewed)
                                        {{$item->review}}
                                        @else
                                        <textarea name="reviews[{{$index}}][review]" class="form-control {{$errors->has('reviews.'.$index.'.review') ? 'is-invalid':''}}"
                                                  style="resize: none"></textarea>
                                        @if($errors->has('reviews.'.$index.'.review'))
                                            @foreach($errors->get('reviews.'.$index.'.review') as $msg)
                                                <span class="invalid-feedback">{{$msg}}</span>
                                            @endforeach
                                        @endif
                                    @endif
                                </th>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr class="text-center">
                            <td colspan="3">
                                @if(!$order->reviewed)
                                <button type="submit" class="btn btn-primary">提交</button>
                                    @else
                                    <a class="btn btn-primary" href="{{route('orders.show',$order->id)}}">查看订单</a>
                                @endif
                            </td>
                        </tr>
                        </tfoot>
                    </form>
                </table>
            </div>
        </div>
    </div>
@endsection
