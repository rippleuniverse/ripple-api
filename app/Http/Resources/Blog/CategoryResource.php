<?php

namespace App\Http\Resources\Blog;

use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin BlogCategory */
class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }
}
