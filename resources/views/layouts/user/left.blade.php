<div class="list-group">
    <a href="{{route('user.addresses.index')}}" class="list-group-item @if(url()->current() == route('user.addresses.index')) active @endif">收货地址</a>
    <a href="{{route('orders.index')}}" class="list-group-item @if(url()->current() == route('orders.index')) active @endif">我的订单</a>
</div>
