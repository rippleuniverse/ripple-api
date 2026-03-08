<?php

namespace App\Http\Resources\Program;

use App\Models\Program;
use App\Traits\Files;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Program */
class ProgramResource extends JsonResource
{
    use Files;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'author' => $this->author,
            'skills' => explode(',', $this->skills),
            'experience_level' => $this->experience_level,
            'category' => [
                'id' => (string)$this->program_category_id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ],
            'price' => sanitizedJsonDecode($this->price, true),
            'rating' => [
                'avg_rating' => (int)$this->ratings()->avg('rating'),
                'count' => $this->ratings()->count()
            ],
            'featured_image' => str_replace(config('app.url') . '//', config('app.url') . '/', $this->getFilePath($this->featured_image)),
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
