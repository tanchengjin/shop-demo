<?php

namespace App;

use App\Exceptions\CouponCodeException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class UserCoupon extends Model
{
    use SoftDeletes;
    public const UPDATED_AT = null;

    protected $fillable = [
        'used_at'
    ];


    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_code_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function checkAvailable($orderAmount = null)
    {
        //own user
        if ($this->user_id !== Auth::id()) {
            throw new CouponCodeException('该优惠券不属于您');
        }
        #coupon verify
        $this->coupon->checkAvailable($orderAmount);
    }
}
