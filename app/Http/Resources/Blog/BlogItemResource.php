<?php

namespace App\Http\Resources\Blog;

use App\Models\Blog;
use App\Traits\Files;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Blog */
class BlogItemResource extends JsonResource
{
    use Files;

    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'author' => $this->author,
            'featured_image' => $this->getFilePath($this->featured_image),
            'created_at' => $this->created_at->format('M d, Y'),
            'read_time' => $this->read_time,
            'category' => [
                'id' => (string)$this->blog_category_id,
                'name' => $this->category->name
            ],
        ];
    }
}
