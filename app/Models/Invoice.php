<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'user_id',
        'payment_url',
        'trx_id',
        'amount',
        'billing_information',
        'status',
        'shipping_fee',
        'currency',
        'payment_method',
        'metadata',
        'discount',
        'coupon_id'
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function getStatusTitleAttribute(): string
    {
        $statusMap = [
            'pending' => 'Pending',
            'in_transit' => 'In Transit',
            'delivered' => 'Delivered',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled'
        ];

        return $statusMap[$this->status];
    }


    public function getSubTotalAttribute(): float|int
    {
        $items = InvoiceItem::where('invoice_id', $this->id)
            ->get()
            ->toArray();

        $totalPrices = array_map(function ($item) {
            return (float)$item['unit_price'] * $item['quantity'];
        }, $items);

        return array_sum($totalPrices) - (float)$this->discount;
    }

    protected function casts(): array
    {
        return [
            'billing_information' => 'array',
        ];
    }
}
