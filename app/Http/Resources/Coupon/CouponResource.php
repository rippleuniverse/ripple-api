<?php

namespace App\Http\Resources\Coupon;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Coupon */
class CouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'code' => $this->code,
            'is_active' => (bool)$this->is_active,
            'type' => $this->type,
            'percentage_value' => (float)$this->percentage_value,
            'fixed_value' => sanitizedJsonDecode($this->fixed_value)
        ];
    }
}
