<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'product_category_id',
        'featured_image',
        'type',
        'title',
        'description',
        'price',
        'about',
        'benefits',
        'target_users',
        'how_to_use',
        'access_delivery',
    ];

    public function scopeFilter(Builder $builder)
    {
        $builder->when(request('search'), function ($query, $search) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('about', 'like', "%{$search}%");
        });

        $builder->when(request('category_id'), function ($query, $categoryId) {
            $query->where('product_category_id', $categoryId);
        });

        $builder->when(request('type'), function ($query, $type) {
            $query->where('type', $type);
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    protected function casts(): array
    {
        return [
            'benefits' => 'array',
            'target_users' => 'array',
            'how_to_use' => 'array',
            'access_delivery' => 'array',
        ];
    }
}
