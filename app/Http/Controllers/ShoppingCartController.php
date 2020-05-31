<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShoppingCartRequest;
use App\Services\ShoppingCartService;
use App\ShoppingCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShoppingCartController extends Controller
{
    public function index()
    {
        $carts=Auth::user()->cart()->get();
        return view('carts.index',compact('carts'));
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
