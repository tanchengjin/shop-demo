<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShoppingCartRequest;
use App\Services\ShoppingCartService;
use App\ShoppingCart;
use App\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShoppingCartController extends Controller
{
    public function index()
    {
        $carts=Auth::user()->cart()->with(['sku','sku.product'])->orderby('id','desc')->get();
        $addresses=UserAddress::query()->orderBy('last_used_at','desc')->get();
        return view('carts.index',compact('carts','addresses'));
    }

    public function store(ShoppingCartRequest $request,ShoppingCartService $cartService)
    {
        $cartService->add($request->input('id'),$request->input('amount'));

        return [];
    }

    public function destroy($id,ShoppingCartService $cartService)
    {
        return $cartService->remove($id);
    }
}
