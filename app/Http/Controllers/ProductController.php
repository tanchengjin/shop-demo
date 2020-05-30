<?php

namespace App\Http\Controllers;

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
        return view('products.show',compact('product'));
    }
}
