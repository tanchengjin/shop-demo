@if(isset($category['children']))
    <li class="dropdown-submenu">
        <a href="{{route('products.index',['category_id'=>$category['id']])}}" class="dropdown-toggle" data-toggle="dropdown">{{$category['name']}}</a>
        <ul class="dropdown-menu">
            @each('layouts.category',$category['children'],'category')
        </ul>
    </li>
@else
    <li><a href="{{route('products.index',['category_id'=>$category['id']])}}">{{$category['name']}}</a></li>
@endif
