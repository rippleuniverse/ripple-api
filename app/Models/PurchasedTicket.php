<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchasedTicket extends Model
{
    protected $fillable = [
        'event_ticket_id',
        'quantity',
        'unit_price',
        'uid',
        'invoice_id',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $uid = uniqid('RIP-');
            $modelExists = PurchasedTicket::where('uid', $uid)->exists();
            if ($modelExists) {
                $lastModelId = (int)PurchasedTicket::latest()->first()->id;
                $newUid = $uid . ($lastModelId + 1);
            } else {
                $newUid = $uid;
            }

            $model->uid = $newUid;
        });
    }

    public function getTotalAttribute(): float
    {
        return (float)$this->unit_price * (int)$this->quantity;
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(EventTicket::class, 'event_ticket_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }


}
