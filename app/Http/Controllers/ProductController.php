<?php

namespace App\Http\Controllers;

use App\OrderItem;
use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products=Product::query()->where('on_sale',1)->paginate(16);
        return view('products.index',compact(['products']));
    }

    public function show($id,Request $request)
    {
        $product=Product::query()
            ->where('on_sale',1)
            ->with(['sku'])
            ->find($id);
        $favorite=false;
        if($request->user()){
            $favorite=$request->user()->favorites()->find($product->id);
        }
        $reviews=OrderItem::query()->with(['product','sku','order.user'])
            ->where('product_id',$id)
            ->whereNotNull('reviewed_at')
            ->orderBy('reviewed_at','desc')
            ->get();
        return view('products.show',compact('product','favorite','reviews'));
    }
    #商品收藏
    public function favorite(Product $product,Request $request)
    {
        if($request->user()->favorites()->find($product->id)){
            return [];
        }
        $request->user()->favorites()->attach($product);
        return [];
    }
    #商品取消收藏
    public function disFavor(Product $product,Request $request)
    {
        $request->user()->favorites()->detach($product);
        return [];
    }

    public function favorList(Request $request)
    {
        $favorites=$request->user()->favorites()->get();
        return view('layouts.user.favorite.index',compact('favorites'));
    }
}
