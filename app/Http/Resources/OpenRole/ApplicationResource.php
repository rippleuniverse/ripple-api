<?php

namespace App\Http\Resources\OpenRole;

use App\Models\JobApplication;
use App\Traits\Files;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin JobApplication */
class ApplicationResource extends JsonResource
{
    use Files;

    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'cv' => $this->getFilePath($this->cv),
            'personal_url' => $this->personal_url,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'name' => $this->name,
            'role' => [
                'id' => (string)$this->open_role_id,
                'name' => $this->role->name,
                'company_name' => $this->role->company_name,
                'type' => $this->role->type,
                'experience_level' => $this->role->experience_level,
                'style' => $this->role->style,
            ],
        ];
    }
}
