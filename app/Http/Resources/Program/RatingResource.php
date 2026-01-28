<?php

namespace App\Http\Resources\Program;

use App\Models\Rating;
use App\Traits\Files;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Rating */
class RatingResource extends JsonResource
{
    use Files;

    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'review' => $this->review,
            'rating' => $this->rating,
            'created_at' => $this->created_at->diffForHumans(),
            'user' => $this->user ? [
                'full_name' => $this->user->full_name,
                'avatar' => $this->getFilePath($this->user->avatar),
            ] : null
        ];
    }
}
