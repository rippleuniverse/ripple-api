<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $fillable = [
        'name',
        'author',
        'experience_level',
        'program_category_id',
        'price',
        'featured_image',
        'id',
        'description',
        'skills',
    ];

    public function scopeFilter(Builder $builder): void
    {
        $builder->when(request('search'), function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('author', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });

        $builder->when(request('category_id'), function ($query, $categoryId) {
            $query->where('program_category_id', $categoryId);
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProgramCategory::class, 'program_category_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
}
