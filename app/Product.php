<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class Product extends Model
{
    use SoftDeletes;

    const TYPE_NORMAL = 'normal';
    const TYPE_CROWDFUNDING = 'crowdfunding';
    const TYPE_SECKILL = 'seckill';

    public static $typeMap = [
        self::TYPE_NORMAL => '普通商品',
        self::TYPE_CROWDFUNDING => '众筹商品',
        self::TYPE_SECKILL => '秒杀商品'
    ];
    protected $fillable = [
        'title', 'description', 'min_price',
        'max_price', 'sold_count', 'review_count',
        'image', 'on_sale', 'rating', 'type'
    ];

    protected $appends = [
        'full_image'
    ];

    protected $casts = [
        'on_sale' => 'boolean'
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

    public function crowdfunding()
    {
        return $this->hasOne(CrowdfundingProduct::class, 'product_id', 'id');
    }

    public function seckill()
    {
        return $this->hasOne(Seckill::class,'product_id','id');
    }
}
