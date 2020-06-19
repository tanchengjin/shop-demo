<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Seckill extends Model
{
    protected $fillable = ['start_at', 'end_at'];

    public $timestamps = false;

    protected $dates = ['start_at', 'end_at'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }


    public function getIsBeforeStartAttribute()
    {
        return Carbon::now()->lt($this->start_at);
    }

    public function getIsAfterEndAttribute()
    {
        return Carbon::now()->gt($this->end_at);
    }
}
