@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <form action="{{route('products.index')}}" class="form-search">
                    <input type="hidden" name="filters">
                    <div class="form-row">
                        <div class="col-md-9">
                            <div class="form-row">
                                <div class="col-auto">
                                    <input type="text" name="q" class="form-control">

                                </div>
                                <button class="btn btn-primary">搜索</button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="order" id="" class="form-control order">
                                <option value="">排序方式</option>
                                <option value="price_asc">价格升序</option>
                                <option value="price_desc">价格降序</option>
                                <option value="sold_asc">销量升序</option>
                                <option value="sold_desc">销量降序</option>
                                <option value="review_asc">评价升序</option>
                                <option value="review_desc">评价降序</option>
                            </select>
                        </div>
                    </div>
                </form>


                <div class="filter-box">
                    @foreach($propertyFilters as $name=>$value)
                        <span class="filter">
                                        {{$name}}:
                                        <span class="filter-value">{{$value}}</span>
                                        <a href="#" class="removeProperty" data-key="{{$name}}" data-value="{{$value}}">X</a>
                        </span>
                    @endforeach

                    @if($category && $category->is_directory)
                    @endif

                    @if(isset($properties))
                        @foreach($properties as $property)
                            <div class="row">
                                <div class="col-xs-3 col-md-3 filter-key">{{$property['key']}}:</div>
                                <div class="col-xs-9 col-md-9 filter-value">
                                    @foreach($property['values'] as $value)
                                        <a href="#" class="property" data-key="{{$property['key']}}"
                                           data-value="{{$value}}">{{$value}}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                    @endif
                </div>

                <div class="products-content">
                    <div class="row">
                        @foreach($products as $product)
                            <div class="col-md-3 col-sm-6">
                                <div class="product-item">
                                    <div class="top">
                                        <div>
                                            <a href="{{route('products.show',$product->id)}}">
                                                <img src="{{$product->full_image}}" alt="{{$product->title}}">
                                            </a>
                                        </div>
                                        <div class="price">￥{{$product->min_price}}</div>
                                        <div class="title"><a
                                                href="{{route('products.show',$product->id)}}">{{$product->title}}</a>
                                        </div>
                                    </div>
                                    <div class="bottom">
                                        <div class="sold_count">销量：<span>{{$product->sold_count}}</span></div>
                                        <div class="review_count">评价：<span>{{$product->review_count}}</span></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="float-right">
                    {{$products->appends($data)->render()}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        var filters = {!! json_encode($data) !!}
        $(document).ready(function () {

            function parseUrl() {
                var searches = {};
                location.search.substr(1).split('&').forEach(function (str) {
                    var result = str.split('=');
                    searches[decodeURIComponent(result[0])] = decodeURIComponent(result[1]);
                });
                return searches;
            }

            function buildSearch(searches) {
                var query = '?';
                _.forEach(searches, function (value, key) {
                    query += encodeURIComponent(key) + '=' + encodeURIComponent(value) + '&';
                });
                return query.substr(0, query.length - 1);
            }

            $('.order').on('change', function () {
                var searches = parseUrl();
                if (searches['filters']) {
                    $('.form-search input[name=filters]').val(searches['filters'])

                }
                $('.form-search').submit();
            });

            $('select[name=order]').val('{{$data['order']}}');
            $('input[name=q]').val('{{$data['search']}}');

            $(".property").click(function () {

                var name = $(this).data('key')
                var value = $(this).data('value')

                var searches = parseUrl();
                if (searches['filters']) {
                    searches['filters'] += '|' + name + ':' + value;
                } else {
                    searches['filters'] = name + ':' + value;
                }

                location.search = buildSearch(searches);
                return;
            });

            $('.removeProperty').click(function () {
                var name = $(this).data('key')

                var searches = parseUrl();
                if (!searches) {
                    return;
                }

                var filters = [];
                searches['filters'].split('|').forEach(function (filter) {
                    var result = filter.split(':');
                    if (result[0] === name) {
                        return;
                    }
                    filters.push(filter);
                });

                searches['filters'] = filters.join('|')

                location.search = buildSearch(searches)
            });
        });
    </script>
@endsection
