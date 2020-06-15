<?php

namespace App\Http\Controllers;

use App\Coupon;
use App\Exceptions\CouponCodeException;
use App\UserCoupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    public function index()
    {
        $userCoupons = Auth::user()->userCoupon()
            ->with(['coupon'])->get();

        return view('layouts.user.coupons.index', compact('userCoupons'));
    }


    /**
     * @param Request $request
     * @return bool
     * @throws CouponCodeException
     */
    public function acquireCoupon(Request $request)
    {
        $coupon = Coupon::acquireCouponValidate($request->input('code', ''));

        try {
            DB::transaction(function () use ($coupon) {
                $userCoupon = new UserCoupon();

                $userCoupon->coupon()->associate($coupon);
                $userCoupon->user()->associate(Auth::user());
                $userCoupon->save();
                $coupon->changeUsed();
            });
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }
        return true;
    }
}
