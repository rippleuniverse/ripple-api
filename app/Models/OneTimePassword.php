<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OneTimePassword extends Model
{
    protected $fillable = [
        'code',
        'user_id',
        'expires_at',
        'type',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

//        Auto generate code
        static::creating(function ($model) {
            $model->code = random_int(1000, 9999);
            $model->expires_at = now()->addMinutes(30);
        });
    }

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }
}
