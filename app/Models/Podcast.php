<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Podcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'featured_image',
        'title',
        'description',
        'podcast_category_id',
        'audio',
        'duration_in_minutes'
    ];

    public function scopeFilter(Builder $builder)
    {
        $builder->when(request('search'), function ($query, $search) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
        $builder->when(request('podcast_category_id'), function ($query, $categoryId) {
            $query->where('podcast_category_id', $categoryId);
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PodcastCategory::class, 'podcast_category_id');
    }
}
