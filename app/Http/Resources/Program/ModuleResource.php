<?php

namespace App\Http\Resources\Program;

use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Module */
class ModuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'module_no' => $this->module_no,
            'title' => $this->title,
            'description' => $this->description,
        ];
    }
}
