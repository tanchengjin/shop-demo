<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'description', 'min_price',
        'max_price', 'sold_count', 'review_count',
        'image', 'on_sale',
    ];

    public function sku()
    {
        return $this->hasMany(ProductSku::class,'product_id','id');
    }
}
