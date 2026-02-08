<?php

namespace App\Http\Resources\OpenRole;

use App\Models\OpenRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin OpenRole */
class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'name' => $this->name,
            'company_name' => $this->company_name,
            'company_location' => $this->company_location,
            'type' => $this->type,
            'experience_level' => $this->experience_level,
            'style' => $this->style,
            'salary' => $this->salary,
            'description' => $this->description,
            'about_company' => $this->about_company,
            'responsibilities' => sanitizedJsonDecode($this->responsibilities),
            'requirements' => sanitizedJsonDecode($this->requirements),
            'benefits' => sanitizedJsonDecode($this->benefits),
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
