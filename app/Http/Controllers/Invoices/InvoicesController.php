<?php

namespace App\Http\Controllers\Invoices;

use App\Enums\StatusCode;
use App\Http\Controllers\Controller;
use App\Http\Resources\Invoice\PurchasedItemResource;
use App\Models\InvoiceItem;
use App\Models\Program;
use App\Traits\Pagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class InvoicesController extends Controller
{
    use Pagination;

    public function viewAllPurchases(Request $request)
    {
        $invoiceItems = InvoiceItem::filter()->latest()->paginate(12);
        $list = PurchasedItemResource::collection($invoiceItems);

        $data = $this->paginatedData($invoiceItems, $list);

        return $this->success($data);
    }

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

    public function downloadProgramFile(InvoiceItem $item)
    {
        $authorized = Gate::allows('view-program', $item);
        if (!$authorized) {
            return $this->failed(null, StatusCode::Forbidden->value, 'Unauthorized.');
        }
        $program = Program::find($item->product_id);
        $file = Storage::disk('private')->get($program->file);
        $fileType = Storage::disk('private')->mimeType($program->file);
        $fileName = basename($program->file);
        return response($file, 200)
            ->header('Content-Type', $fileType)
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');

    }
}
