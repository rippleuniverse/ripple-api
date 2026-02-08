<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_location',
        'company_name',
        'type',
        'experience_level',
        'style',
        'salary',
        'description',
        'about_company',
        'responsibilities',
        'requirements',
        'benefits',
    ];

    public function scopeFilter(Builder $builder): void
    {
        $builder->when(request('search'), function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%")
                ->orWhere('company_location', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('about_company', 'like', "%{$search}%");
        });

        $builder->when(request('type'), function ($query, $type) {
            $query->where('type', $type);
        });

        $builder->when(request('experience_level'), function ($query, $level) {
            $query->where('experience_level', $level);
        });

        $builder->when(request('style'), function ($query, $style) {
            $query->where('style', $style);
        });
    }

    protected function casts(): array
    {
        return [
            'responsibilities' => 'array',
            'requirements' => 'array',
            'benefits' => 'array',
        ];
    }
}
