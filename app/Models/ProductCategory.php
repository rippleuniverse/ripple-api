<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (ProductCategory $model) {
            if ($model->slug) return;

            $slug = Str::slug($model->name);
            $slugExists = ProductCategory::where('slug', $slug)->exists();

            if ($slugExists) {
                $lastSlugId = (int)ProductCategory::latest()->first()->id;
                $slugStr = $slug . '-' . ($lastSlugId + 1);
            } else {
                $slugStr = $slug;
            }

            $model->slug = $slugStr;
        });
    }


    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'product_category_id');
    }

}
