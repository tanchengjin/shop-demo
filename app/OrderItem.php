<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable=[
        'amount','price','review','reviewed_at','rating'
    ];
    public $timestamps=false;


    public function order()
    {
        return $this->belongsTo(Order::class,'order_id','id');
    }

    public function sku()
    {
        return $this->belongsTo(ProductSku::class,'product_sku_id','id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }
}
