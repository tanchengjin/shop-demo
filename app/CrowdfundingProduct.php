<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrowdfundingProduct extends Model
{
    const STATUS_FUNDING = 'funding';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';

    public static $statusMap = [
        self::STATUS_FUNDING => '众筹中',
        self::STATUS_SUCCESS => '众筹成功',
        self::STATUS_FAIL => '众筹失败'
    ];
    public $timestamps = false;

    protected $fillable = [
        'target_amount', 'current_amount', 'user_count', 'end_at', 'status',
    ];
    protected $dates=['end_at'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }


    public function getPercentAttribute()
    {
        $amount = $this->attributes['current_amount'] / $this->attributes['target_amount'];

        return floatval(number_format($amount * 100, 2));
    }

}
