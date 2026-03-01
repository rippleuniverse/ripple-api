<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'quantity',
        'unit_price',
        'product_id',
        'product_type',
        'currency'
    ];


    public const PRODUCT_MODELS = [
        'shop' => Product::class,
        'program' => Program::class,
        'event' => Event::class,
    ];

    public function scopeFilter(Builder $builder)
    {
        $builder->when(request('product_type'), function ($query, $productType) {
            $query->where('product_type', $productType);
        });
    }


    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getItemAttribute()
    {
        return $this::PRODUCT_MODELS[$this->product_type]::find($this->product_id);
    }

    public function getProductNameAttribute(): string
    {
        return $this->product_type === 'program' ? $this->item->name : $this->item->title;
    }

    public function getTotalAttribute(): float
    {
        return $this->quantity * (float)$this->unit_price;
    }
}
