<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class EventCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (EventCategory $model) {
            $slug = Str::slug($model->name);
            $slugExists = EventCategory::where('slug', $slug)->exists();

            if ($slugExists) {
                $lastSlugId = (int)EventCategory::latest()->first()->id;
                $slugStr = $slug . '-' . ($lastSlugId + 1);
            } else {
                $slugStr = $slug;
            }

            $model->slug = $slugStr;
        });
    }


    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'event_category_id');
    }
}
