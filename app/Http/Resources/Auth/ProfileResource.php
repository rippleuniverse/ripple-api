<?php

namespace App\Http\Resources\Auth;

use App\Models\User;
use App\Traits\Files;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class ProfileResource extends JsonResource
{
    use Files;

    public function toArray(Request $request): array
    {
        return [
            'full_name' => $this->full_name,
            'email' => $this->email,
            'role' => $this->role,
            'avatar' => $this->getFilePath($this->avatar),
            'email_verified_at' => $this->email_verified_at,
        ];
    }
}
