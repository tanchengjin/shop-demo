<?php

namespace App\Http\Controllers;

use App\Category;
use App\OrderItem;
use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $product = Product::query()->where('on_sale', 1);
        $data = [
            'search' => '',
            'order' => ''
        ];
        if ($search = $request->input('q', '')) {
            $product->where(function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('sku', function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
            $data['search'] = $search;
        }

        if ($order = $request->input('order', '')) {
            $data['order'] = $order;
            preg_match('/^(.+)_(asc|desc)$/', $order, $m);

            if (in_array($m[1], ['price', 'sold', 'review'])) {
                if ($m[1] === 'price') {
                    $m[1] = 'min_price';
                }

                if ($m[1] === 'review') {
                    $m[1] = 'review_count';
                }

                if ($m[1] === 'sold') {
                    $m[1] = 'sold_count';
                }

                $product->orderBy($m[1], $m[2]);
            }

        }
        $products = $product->paginate(16);
        $categoryTree=Category::categoryTree();
        return view('products.index', compact(['products', 'data','categoryTree']));
    }

    public function show($id, Request $request)
    {
        $product = Product::query()
            ->where('on_sale', 1)
            ->with(['sku'])
            ->find($id);
        $favorite = false;
        if ($request->user()) {
            $favorite = $request->user()->favorites()->find($product->id);
        }
        $reviews = OrderItem::query()->with(['product', 'sku', 'order.user'])
            ->where('product_id', $id)
            ->whereNotNull('reviewed_at')
            ->orderBy('reviewed_at', 'desc')
            ->get();
        return view('products.show', compact('product', 'favorite', 'reviews'));
    }

    #商品收藏
    public function favorite(Product $product, Request $request)
    {
        if ($request->user()->favorites()->find($product->id)) {
            return [];
        }
        $request->user()->favorites()->attach($product);
        return [];
    }

    #商品取消收藏
    public function disFavor(Product $product, Request $request)
    {
        $request->user()->favorites()->detach($product);
        return [];
    }

    public function favorList(Request $request)
    {
        $favorites = $request->user()->favorites()->get();
        return view('layouts.user.favorite.index', compact('favorites'));
    }
}
