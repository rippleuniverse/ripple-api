<?php

namespace App\Http\Resources\Invoice;

use App\Models\InvoiceItem;
use App\Traits\Files;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin InvoiceItem */
class PurchasedItemResource extends JsonResource
{
    use Files;

    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'quantity' => $this->quantity,
            'unit_price' => currencyFormat($this->unit_price, $this->invoice->currency),
            'product_id' => $this->product_id,
            'created_at' => $this->created_at->format('M d, Y'),
            'invoice' => [
                'id' => (string)$this->invoice_id,
                'status' => $this->invoice->status,
                'created_at' => $this->invoice->created_at->format('M d, Y'),
                'trx_id' => $this->invoice->trx_id,
                'payment_method' => $this->invoice->payment_method,
                'payment_url' => $this->invoice->payment_url,
                'metadata' => $this->invoice->metadata ? sanitizedJsonDecode($this->invoice->metadata, true) : null,
            ],
            'product_type' => $this->product_type,
            'item' => $this->item ? [
                'featured_image' => $this->getFilePath($this->item->featured_image),
                'name' => $this->product_type === 'program' ? $this->item->name : $this->item->title
            ] : null,
            'total' => currencyFormat($this->total, $this->invoice->currency),
            'user' => $this->invoice->user ? [
                'id' => (string)$this->invoice->user_id,
                'full_name' => $this->invoice->user->full_name,
                'email' => $this->invoice->user->email,
            ] : null
        ];
    }
}
