<?php

namespace App\Http\Resources\Event;

use App\Models\Event;
use App\Traits\Files;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Event */
class EventResource extends JsonResource
{
    use Files;

    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'featured_image' => $this->getFilePath($this->featured_image),
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date->format('Y-m-d'),
            'access' => $this->access,
            'type' => $this->type,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
