<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders=Auth::user()->order()->orderBy('created_at','desc')->paginate(16);
        return view('orders.index',compact('orders'));
    }

    public function store(OrderRequest $request,OrderService $orderService)
    {
        $remark=$request->input('remark')?:null;
        $items=$request->input('items');
        $address_id=$request->input('address_id');

        return $orderService->store($items,$address_id,$remark);
    }
}
