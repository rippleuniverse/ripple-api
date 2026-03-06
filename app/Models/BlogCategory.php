<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (BlogCategory $model) {
            $slug = Str::slug($model->name);
            $slugExists = BlogCategory::where('slug', $slug)->exists();

            if ($slugExists) {
                $lastSlugId = (int)BlogCategory::latest()->first()->id;
                $slugStr = $slug . '-' . ($lastSlugId + 1);
            } else {
                $slugStr = $slug;
            }

            $model->slug = $slugStr;
        });
    }

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class, 'blog_category_id');
    }
}
