<?php

namespace App\Http\Resources\Product;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ProductCategory */
class CategoryProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'products' => ProductItemResource::collection($this->products()->latest()->take(3)->get()),
        ];
    }
}
