<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'description', 'min_price',
        'max_price', 'sold_count', 'review_count',
        'image', 'on_sale', 'rating'
    ];

    protected $appends = [
        'full_image'
    ];

    protected $casts=[
        'on_sale'=>'boolean'
    ];
    public function sku()
    {
        return $this->hasMany(ProductSku::class, 'product_id', 'id');
    }

    public function getFullImageAttribute()
    {
        $prefix = substr($this->image, 0, 4);
        if ($prefix !== 'http') {
            return '/storage/' . $this->image;
        }
        return $this->image;
    }

    public function categories()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function properties()
    {
        return $this->hasMany(ProductProperty::class, 'product_id', 'id');
    }

    public function toESArray()
    {
        $arr = Arr::only($this->toArray(), [
            'id',
            'type',
            'title',
            'category_id',
            'long_title',
            'on_sale',
            'rating',
            'sold_count',
            'review_count',
            'min_price',
        ]);

        $arr['category'] = $this->category ? explode('-', $this->category->full_name) : '';

        $arr['category_path'] = $this->category ? $this->category->path : '';


        $arr['description'] = strip_tags($this->desciption);

        $arr['sku'] = $this->sku->map(function (ProductSku $sku) {
            return Arr::only($sku->toArray(), ['title', 'description', 'price']);
        });

        $arr['properties'] = $this->properties->map(function (ProductProperty $productProperty) {
            return Arr::only($productProperty->toArray(), ['name', 'value']);
        });

        return $arr;
    }
}
