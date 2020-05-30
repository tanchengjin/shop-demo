<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'title', 'description', 'price', 'stock'
    ];
}
