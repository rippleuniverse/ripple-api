<?php

namespace App\Policies;

use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoiceItemPolicy
{
    use HandlesAuthorization;


    public function view(User $user, InvoiceItem $invoiceItem): bool
    {
        $isOwner = (int)$user->id === (int)$invoiceItem->invoice->user_id;
        return $user->role === 'admin' || $isOwner;
    }

    public function viewProgram(User $user, InvoiceItem $invoiceItem): bool
    {
        return (int)$invoiceItem->invoice->user_id === (int)$user->id && $invoiceItem->invoice->status === 'paid';
    }

}
