<?php

namespace App\Http\Resources\Podcast;

use App\Models\Podcast;
use App\Traits\Files;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Podcast */
class PodcastResource extends JsonResource
{
    use Files;

    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'featured_image' => $this->getFilePath($this->featured_image),
            'title' => $this->title,
            'description' => $this->description,
            'audio' => $this->getFilePath($this->audio),
            'created_at' => $this->created_at->format('Y-m-d'),
            'duration_in_minutes' => (int)$this->duration_in_minutes,
            'category' => [
                'id' => (string)$this->podcast_category_id,
                'name' => $this->category->name
            ],
        ];
    }
}
