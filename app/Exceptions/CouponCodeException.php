<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Throwable;

class CouponCodeException extends Exception
{
    public function __construct($message, $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render(Request $request)
    {
        if($request->expectsJson()){
            return response()->json(['message'=>$this->message],$this->code);
        }

        return redirect()->back()->withErrors(['coupon_code'=>$this->message]);
    }
}
