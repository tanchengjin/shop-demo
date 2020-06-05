<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    public $timestamps=false;
    protected $fillable=[
        'address','province','city','district','contact_name','contact_phone',
        'zip'
    ];
}
