<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'title', 'description', 'price', 'stock'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }
}
