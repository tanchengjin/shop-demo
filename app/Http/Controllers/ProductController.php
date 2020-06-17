<?php

namespace App\Http\Controllers;

use App\Category;
use App\OrderItem;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $prePage = 16;

        $params = [
            'index' => 'products',
            'type' => '_doc',
            'body' => [
                'from' => ($page - 1) * $prePage,
                'size' => $prePage,
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['on_sale' => true]],
                        ]
                    ]
                ],
            ]
        ];

        if ($order = $request->input('order', '')) {
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

                $params['body']['sort'] = [$m[1] => $m[2]];
            }
        }

        if ($request->input('category_id') && $category = Category::find($request->input('category_id'))) {
            if ($category->is_directory) {
                $params['body']['query']['bool']['filter'][] = [
                    'prefix' => ['category_path' => $category->path . $category->id . '-']
                ];
            } else {
                $params['body']['query']['bool']['filter'][] = ['term' => ['category_id' => $category->id]];
            }

        }


        if ($search = $request->input('q', '')) {
            $params['body']['query']['bool']['must'] = [
                [
                    'multi_match' => [
                        'query' => $search,
                        'fields' => [
                            'title^3',
                            'long_title^2',
                            'category^2', // 类目名称
                            'description',
                            'skus.title',
                            'skus.description',
                            'properties.value',
                        ]
                    ],
                ]
            ];
        }


        if ($search || isset($category)) {
            $params['body']['aggs'] = [
                'properties' => [
                    'nested' => [
                        'path' => 'properties'
                    ],
                    'aggs' => [
                        'properties' => [
                            'terms' => [
                                'field' => 'properties.name'
                            ],
                            'aggs' => [
                                'value' => [
                                    'terms' => [
                                        'field' => 'properties.value'
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            ];
        }

        $propertyFilter = [];

        if ($filterString = $request->input('filters')) {
            $filterArray = explode('|', $filterString);
            foreach ($filterArray as $filter) {
                list($name, $value) = explode(':', $filter);

                $propertyFilter[$name] = $value;
                $params['body']['query']['bool']['filter'][] = [
                    'nested' => [
                        'path' => 'properties',
                        'query' => [
                            ['term' => ['properties.name' => $name]],
                            ['term' => ['properties.value' => $value]]
                        ],
                    ],
                ];
            }
        }


        $result = app('es')->search($params);

        if (isset($result['aggregations'])) {
            $properties = collect($result['aggregations']['properties']['properties']['buckets'])->map(function ($bucket) {
                return [
                    'key' => $bucket['key'],
                    'values' => collect($bucket['value']['buckets'])->pluck('key')->all()
                ];
            })->filter(function ($property) use ($propertyFilter) {

                return !isset($propertyFilter[$property['key']]);
            });
        }


        $productIds = collect($result['hits']['hits'])->pluck('_id')->all();

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->orderByRaw(sprintf("FIND_IN_SET(id,'%s')", join(',', $productIds)))
            ->get();

        $pager = new LengthAwarePaginator($products, $result['hits']['total'], $prePage, $page, [
            'path' => route('products.index', false)
        ]);
        return view('products.index', [
            'products' => $pager,
            'data' => [
                'search' => $search,
                'order' => $order,
            ],
            'category' => $category ?? null,
            'properties' => $properties??null,
            'propertyFilters'=>$propertyFilter
        ]);


    }

    public function oldIndex(Request $request)
    {
        $product = Product::query()->where('on_sale', 1);

        $data = [
            'search' => '',
            'order' => ''
        ];

        #分类
        if ($request->input('category_id', '') && $category = Category::query()->find($request->input('category_id'))) {
            if ($category->is_directory) {
                $product->whereHas('categories', function ($query) use ($category) {
                    $like = $category->path . $category->id . '-%';
                    $query->where('path', 'like', $like);
                });
            } else {
                $product->where('category_id', $request->input('category_id'));
            }
        }
        #搜索
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
        #排序
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
        $categoryTree = Category::categoryTree();
        return view('products.index', compact(['products', 'data', 'categoryTree']));
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
