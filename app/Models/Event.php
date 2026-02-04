<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'featured_image',
        'title',
        'date',
        'access',
        'type',
        'description',
        'what_to_expect',
        'who_to_expect',
        'facilitators',
        'agendas',
    ];

    public function scopeBuilder(Builder $builder): void
    {
        $builder->when(request('search'), function ($query, $search) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });

        $builder->when(request('type'), function ($query, $type) {
            $query->where('type', $type);
        });
        $builder->when(request('access'), function ($query, $access) {
            $query->where('access', $access);
        });
    }

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'what_to_expect' => 'array',
            'who_to_expect' => 'array',
            'facilitators' => 'array',
            'agendas' => 'array',
        ];
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(EventTicket::class, 'event_id');
    }
}
