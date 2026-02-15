<?php

namespace App\Http\Resources\Product;

use App\Models\Product;
use App\Traits\Files;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Product */
class ProductItemResource extends JsonResource
{
    use Files;

    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'featured_image' => $this->getFilePath($this->featured_image),
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'price' => sanitizedJsonDecode($this->price),
            'about' => $this->about,
            'benefits' => sanitizedJsonDecode($this->benefits),
            'target_users' => sanitizedJsonDecode($this->target_users),
            'how_to_use' => $this->how_to_use,
            'access_delivery' => sanitizedJsonDecode($this->access_delivery),
            'category' => [
                'id' => (string)$this->product_category_id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ],
        ];
    }
}
