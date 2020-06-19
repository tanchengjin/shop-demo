<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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

    public function decrementStock($amount)
    {
        if($amount <= 0){
            throw new \Exception('减库存不能小于0');
        }
        return self::query()->where('id',$this->id)->where('stock','>=',$amount)->decrement('stock',$amount);

    }
}
