<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'featured_image',
        'title',
        'date',
        'access',
        'type',
        'description'
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
        ];
    }
}
