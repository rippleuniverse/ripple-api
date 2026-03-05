<?php

namespace App\Http\Controllers\Coupon;

use App\Enums\StatusCode;
use App\Http\Controllers\Controller;
use App\Http\Resources\Coupon\CouponResource;
use App\Models\Coupon;
use App\Traits\Pagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CouponController extends Controller
{
    use Pagination;

    public function userCoupon(Request $request)
    {
        $user = $request->user();
        $coupon = $user->coupon ? new CouponResource($user->coupon) : null;

        return $this->success($coupon);
    }

    public function viewAll()
    {
        $coupons = Coupon::where('is_created_by_admin', true)->filter()->latest()->paginate(12);
        $list = CouponResource::collection($coupons);
        $data = $this->paginatedData($coupons, $list);

        return $this->success($data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:191', 'unique:coupons,code'],
            'is_active' => ['required', 'boolean'],
            'type' => ['required', 'in:percentage,fixed'],
            'percentage_value' => ['required_if:type,percentage', 'numeric', 'min:0', 'max:100'],
            'fixed_value' => ['required_if:type,fixed', 'array'],
            'fixed_value.*.currency' => ['required_if:type,fixed', 'string', 'in:NGN,USD'],
            'fixed_value.*.amount' => ['required_if:type,fixed', 'numeric:', 'min:0'],
        ]);
        Coupon::create([
            'user_id' => null,
            'code' => $data['code'],
            'is_active' => $data['is_active'],
            'percentage_value' => $data['type'] === 'percentage' ? $data['percentage_value'] : 0,
            'fixed_value' => json_encode($data['type'] === 'fixed' ? $data['fixed_value'] : []),
            'type' => $data['type'],
            'is_created_by_admin' => true,
        ]);

        return $this->success(null, 'Coupon created successfully.');
    }

    public function update(Coupon $coupon, Request $request)
    {
        $authorized = Gate::allows('update', $coupon);

        if (!$authorized) {
            return $this->failed(null, StatusCode::Forbidden->value, 'Unauthorized.');
        }

        $data = $request->validate([
            'code' => ['required', 'string', 'max:191', 'unique:coupons,code,' . $coupon->id],
            'is_active' => ['required', 'boolean'],
            'type' => ['required', 'in:percentage,fixed'],
            'percentage_value' => ['required_if:type,percentage', 'numeric', 'min:0', 'max:100'],
            'fixed_value' => ['required_if:type,fixed', 'array'],
            'fixed_value.*.currency' => ['required_if:type,fixed', 'string', 'in:NGN,USD'],
            'fixed_value.*.amount' => ['required_if:type,fixed', 'numeric:', 'min:0'],
        ]);
        $coupon->update([
            'code' => $data['code'],
            'is_active' => $data['is_active'],
            'percentage_value' => $data['type'] === 'percentage' ? $data['percentage_value'] : 0,
            'fixed_value' => json_encode($data['type'] === 'fixed' ? $data['fixed_value'] : []),
            'type' => $data['type'],
        ]);

        return $this->success(null, 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $authorized = Gate::allows('delete', $coupon);

        if (!$authorized) {
            return $this->failed(null, StatusCode::Forbidden->value, 'Unauthorized.');
        }
        $coupon->delete();
        return $this->success(null, 'Coupon deleted successfully.');
    }

    public function view(Coupon $coupon)
    {
        $authorized = Gate::allows('view', $coupon);

        if (!$authorized) {
            return $this->failed(null, StatusCode::Forbidden->value, 'Unauthorized.');
        }

        $data = new CouponResource($coupon);

        return $this->success($data);
    }
}
