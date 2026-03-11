<?php

namespace App\Http\Resources\User;

use App\Models\User;
use App\Traits\Files;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    use Files;

    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'full_name' => $this->full_name,
            'avatar' => $this->getFilePath($this->avatar),
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d'),
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
