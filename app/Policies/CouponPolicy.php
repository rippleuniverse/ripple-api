<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CouponPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Coupon $coupon): bool
    {
        return $coupon->is_created_by_admin;
    }

    public function update(User $user, Coupon $coupon): bool
    {
        return $coupon->is_created_by_admin;
    }

    public function delete(User $user, Coupon $coupon): bool
    {
        return $coupon->is_created_by_admin;
    }


}
