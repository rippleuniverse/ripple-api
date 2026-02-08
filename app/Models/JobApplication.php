<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'cv',
        'personal_url',
        'open_role_id',
    ];

    public function scopeFilter(Builder $builder)
    {
        $builder->when(request('search'), function ($query, $search) {
            $query->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
        $builder->when(request('open_role_id'), function ($query, $openRoleId) {
            $query->where('open_role_id', $openRoleId);
        });
    }

    public function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(OpenRole::class, 'open_role_id');
    }
}
