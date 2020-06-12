<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'description', 'min_price',
        'max_price', 'sold_count', 'review_count',
        'image', 'on_sale', 'rating'
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
}
