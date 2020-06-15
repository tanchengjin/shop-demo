<?php

namespace App;

use App\Exceptions\CouponCodeException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Coupon extends Model
{
    public $timestamps = false;
    const TYPE_PERCENT = 'percent';
    const TYPE_FIXED = 'fixed';
    public static $typeMap = [
        self::TYPE_PERCENT => '百分比',
        self::TYPE_FIXED => '固定金额'
    ];
    protected $appends = ['description'];

    protected $dates = [
        'start_time',
        'end_time'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'coupon_code_id', 'id');
    }

    public static function createCode($length = 16)
    {
        do {
            $code = $code = Str::random($length);
        } while (Coupon::query()->where('code', $code)->exists());

        return $code;
    }

    public function checkCouponCode($orderAmount = null)
    {
        if (!$this->enable) {
            throw new CouponCodeException('优惠券无效');
        }

        if ($this->used >= $this->total) {
            throw new CouponCodeException('优惠券已被兑完');
        }

        if ($this->not_before && $this->not_before->gt(Carbon::now())) {
            throw new CouponCodeException('该优惠券现在还不能使用');
        }

        if ($this->not_after && $this->not_after->lt(Carbon::now())) {
            throw new CouponCodeException('该优惠券已过期');
        }

        if (!is_null($orderAmount) && $orderAmount < $this->min_amount) {
            throw new CouponCodeException('不符合订单金额');
        }
    }

    public function checkAvailable($orderAmount = null)
    {
        if (!$this->enable) {
            throw new CouponCodeException('优惠券不可用');
        }
        if ($this->start_time->gt(Carbon::now())) {
            throw new CouponCodeException('优惠券未生效');
        }

        if ($this->end_time->lt(Carbon::now())) {
            throw new CouponCodeException('优惠券已过期');
        }

        if (!is_null($orderAmount) && $orderAmount <= $this->min_amount) {
            throw new CouponCodeException('不符合最低使用金额');
        }

        if ($this->used >= $this->total) {
            throw new CouponCodeException('该优惠券已兑完');
        }
    }

    /**
     * @param $orderAmount
     * @param $convert boolean 是否格式化
     * @return string
     */
    public function discount($orderAmount, $convert = true)
    {
        if ($this->type === self::TYPE_FIXED) {
            $price = $orderAmount - $this->value;
        } else {
            $price = $orderAmount * (100 - $this->value) / 100;
        }

        if ($convert) {
            return number_format($price, 2);
        } else {
            return $price;
        }
    }

    /**
     * 增加兑换数量
     * @param bool $increment
     * @return int
     */
    public function changeUsed($increment = true)
    {
        if ($increment) {
            return $this->where('id', $this->id)->where('used', '<', $this->total)->increment('used');
        } else {
            return $this->decrement('used');
        }
    }

    public function getDescriptionAttribute()
    {
        $str = '';
        if ($this->min_amount >= 0.01) {
            $str = '满' . $this->min_amount;

        }

        if ($this->type === self::TYPE_PERCENT) {
            return $str . '优惠' . $this->value . '%';
        }

        return $str . '减' . $this->value;
    }

    #relative
    public function userCoupon()
    {
        return $this->hasMany(UserCoupon::class, 'coupon_code_id', 'id');
    }

    /**
     * 用户兑换优惠券验证规则
     * @param string $code
     * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
     * @throws CouponCodeException
     */
    public static function acquireCouponValidate(string $code)
    {
        if (!$code) {
            throw new CouponCodeException('优惠券兑换码不能为空!');
        }

        if (!$coupon = self::query()->where('code', $code)->first()) {
            throw new CouponCodeException('优惠券不存在!');
        }
        if (!$coupon->enable) {
            throw new CouponCodeException('优惠券无效!');
        }

        if ($coupon->used >= $coupon->total) {
            throw new CouponCodeException('该优惠券已兑完!');
        }

        if ($coupon->end_time->lt(Carbon::now())) {
            throw new CouponCodeException('优惠券已过期!');
        }
        #校验用户是否已经拥有该优惠券
        if (Auth::user()->userCoupon()->where('coupon_code_id', $coupon->id)->exists()) {
            throw new CouponCodeException('您已拥有该优惠券!');
        }

        return $coupon;
    }
}
