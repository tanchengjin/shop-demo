<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    public $timestamps=false;

    protected $fillable=[
        'amount'
    ];

    public function sku()
    {
        return $this->belongsTo(ProductSku::class,'product_sku_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
