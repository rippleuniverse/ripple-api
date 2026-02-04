<?php

namespace App\Http\Resources\Event;

use App\Models\EventTicket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin EventTicket */
class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $price = json_decode($this->price);
        $features = json_decode($this->features);
        return [
            'id' => (string)$this->id,
            'name' => $this->name,
            'price' => gettype($price) === 'string' ? json_decode($price) : $price,
            'features' => gettype($features) === 'string' ? json_decode($features) : $features,
        ];
    }
}
