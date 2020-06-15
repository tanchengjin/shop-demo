<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductProperty extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name', 'value'
    ];


}
