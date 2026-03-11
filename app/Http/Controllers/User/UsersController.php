<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Invoice\PurchasedItemResource;
use App\Http\Resources\User\UserResource;
use App\Models\InvoiceItem;
use App\Models\User;
use App\Notifications\User\StatusChangedNotification;
use App\Traits\Pagination;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    use Pagination;

    public function viewAllUsers()
    {
        $users = User::filter()
            ->where('role', 'user')
            ->latest()->paginate(12);
        $list = UserResource::collection($users);
        $data = $this->paginatedData($users, $list);

        return $this->success($data);
    }

    public function viewUser(User $user, Request $request)
    {
        $data = new UserResource($user);
        $purchases = InvoiceItem::whereHas('invoice', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->latest()->take(5)->get();
        $purchasesData = PurchasedItemResource::collection($purchases);
        $detailedData = [
            ...$data->toArray($request),
            'recent_purchases' => $purchasesData
        ];
        return $this->success($detailedData);
    }

    public function viewStaffs()
    {
        $users = User::filter()
            ->where('role', 'admin')
            ->latest()->paginate(12);
        $list = UserResource::collection($users);
        $data = $this->paginatedData($users, $list);

        return $this->success($data);
    }

    public function viewStaff(User $user, Request $request)
    {
        $data = new UserResource($user);
        return $this->success($data);
    }

    public function changeStatus(User $user, Request $request)
    {
        $data = $request->validate([
            'status' => ['required', 'in:active,suspended'],
            'reason' => ['nullable', 'string'],
        ]);
        $user->update($data);
        $user->notify(new StatusChangedNotification($user, $data['reason']));
        return $this->success(null, 'User status updated successfully.');
    }
}
