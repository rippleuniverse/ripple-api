<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'blog_category_id',
        'description',
        'content',
        'featured_image',
        'slug',
        'author'
    ];

    public function scopeFilter(Builder $builder): void
    {
        $builder->when(request('search'), function ($query, $search) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });

        $builder->when(request('blog_category_id'), function ($query, $categoryId) {
            $query->where('blog_category_id', $categoryId);
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function getReadTimeAttribute(): string
    {
        $readMinutes = strlen($this->content) / 1200;
        return round($readMinutes) . ' mins read';
    }
}
