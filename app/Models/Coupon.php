<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'is_active',
        'is_created_by_admin',
        'user_id',
        'type',
        'coupon_id',
        'percentage_value',
        'fixed_value',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_admin_owned' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function coupon(): HasOne
    {
        return $this->hasOne(Coupon::class, 'coupon_id');
    }

    public function calculateDeduction(string $currency, float $amount): float
    {
        if ($this->type === 'fixed') {
            $price = collect(sanitizedJsonDecode($this->fixed_value));
            $val = (float)$price->where('currency', $currency)->first()->amount;
        } else {

            $val = (float)$this->percentage_value;
        }


        $final = $this->type === 'fixed' ? $val : (($val / 100) * $amount);
        error_log($final);
        return $final;
    }

    public function calculateValue(string $currency, float $amount): float
    {
        return $amount - $this->calculateDeduction($currency, $amount);
    }
}
