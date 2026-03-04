<?php

namespace App\Http\Controllers\Coupon;

use App\Http\Controllers\Controller;
use App\Http\Resources\Coupon\CouponResource;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function userCoupon(Request $request)
    {
        $user = $request->user();
        $coupon = $user->coupon ? new CouponResource($user->coupon) : null;

        return $this->success($coupon);
    }
}
