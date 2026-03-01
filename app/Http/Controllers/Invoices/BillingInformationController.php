<?php

namespace App\Http\Controllers\Invoices;

use App\Http\Controllers\Controller;
use App\Http\Resources\Invoice\BillingInfoResource;
use Illuminate\Http\Request;

class BillingInformationController extends Controller
{

    public function view(Request $request)
    {
        $user = $request->user();
        $info = $user->billingInformation ? new BillingInfoResource($user->billingInformation) : null;
        return $this->success($info);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:191'],
            'last_name' => ['required', 'string', 'max:191'],
            'apartment' => ['required', 'string', 'max:191'],
            'city' => ['required', 'string', 'max:191'],
            'country' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email'],
            'phone' => ['required', 'string', 'max:191'],
        ]);

        $user = $request->user();
        $user->billingInformation()->updateOrCreate([], $data);

        return $this->success(null, 'Billing information saved successfully.');
    }

    public function destroy(Request $request)
    {
        $user = $request->user();
        $user->billingInformation()->delete();
        return $this->success(null, 'Billing information deleted successfully.');
    }
}
