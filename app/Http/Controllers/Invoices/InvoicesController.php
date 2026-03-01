<?php

namespace App\Http\Controllers\Invoices;

use App\Enums\Enums\StatusCode;
use App\Http\Controllers\Controller;
use App\Http\Resources\Invoice\PurchasedItemResource;
use App\Models\InvoiceItem;
use App\Traits\Pagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InvoicesController extends Controller
{
    use Pagination;

    public function purchases(Request $request)
    {
        $user = $request->user();
        $invoiceItems = InvoiceItem::whereHas('invoice', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->filter()->latest()->paginate(12);
        $list = PurchasedItemResource::collection($invoiceItems);

        $data = $this->paginatedData($invoiceItems, $list);

        return $this->success($data);
    }

    public function purchase(InvoiceItem $item)
    {
        $authorized = Gate::allows('view', $item);

        if (!$authorized) {
            return $this->failed(null, StatusCode::Forbidden->value, 'Unauthorized.');
        }
        $data = new PurchasedItemResource($item);

        return $this->success($data);
    }
}
