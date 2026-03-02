<?php

namespace App\Http\Controllers\Invoices;

use App\Enums\Enums\StatusCode;
use App\Http\Controllers\Controller;
use App\Mail\InvoiceMail;
use App\Models\EventTicket;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\ShippingFee;
use App\Models\User;
use App\Traits\Stripe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    use Stripe;


    public function eventCheckout(Request $request)
    {
        $data = $request->validate([
            'currency' => ['required', 'in:USD,NGN'],
            'save_billing_information' => ['required', 'boolean'],
            'billing_information' => ['required', 'array'],
            'billing_information.first_name' => ['required', 'string', 'max:191'],
            'billing_information.last_name' => ['required', 'string', 'max:191'],
            'billing_information.email' => ['required', 'email', 'max:191'],
            'billing_information.apartment' => ['required', 'string', 'max:191'],
            'billing_information.city' => ['required', 'string', 'max:191'],
            'billing_information.country' => ['required', 'string', 'max:191'],
            'billing_information.phone' => ['required', 'string', 'max:191'],
            'tickets' => ['required', 'array', 'min:1'],
            'tickets.*.id' => ['required', 'string', 'exists:event_tickets,id'],
            'tickets.*.quantity' => ['numeric', 'min:1'],
        ]);

        $ticketIds = array_column($data['tickets'], 'id');
        $user = $request->user();
        try {
            DB::beginTransaction();

            $hasUnavailable = $this->hasUnavailableTickets($ticketIds);
            if ($hasUnavailable) {
                return $this->failed(null, StatusCode::BadRequest->value, 'Ticket is unavailable');
            }

            $invoiceItems = [];
            $amount = 0;


            if ($data['save_billing_information']) {
                $this->saveBillingInformation($user, $data['billing_information']);
            }

            foreach ($data['tickets'] as $item) {
                $ticket = EventTicket::find($item['id']);
                $price = collect(sanitizedJsonDecode($ticket->price));
                $amountVal = (float)$price->where('currency', $data['currency'])->first()->amount;
                $amount += $amountVal * $item['quantity'];

                $invoiceItems[] = [
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $amountVal,
                    'product_type' => 'event',
                    'currency' => $data['currency'],
                ];
            }

            $totalAmount = $amount;
            $trxId = Str::uuid();

            $invoice = Invoice::create([
                'amount' => $totalAmount,
                'status' => 'pending',
                'shipping_fee' => 0,
                'user_id' => $user->id,
                'currency' => $data['currency'],
                'billing_information' => json_encode($data['billing_information']),
                'trx_id' => $trxId,
            ]);

            $invoice->items()->createMany($invoiceItems);
            $paymentUrl = $this->generatePaymentLink($invoice);
            DB::commit();

            Mail::to($user->email)->send(new InvoiceMail($invoice));

            return $this->success(['payment_url' => $paymentUrl], 'Invoice created successfully.');

        } catch (\Exception|\Throwable $e) {
            DB::rollBack();
            return $this->failed(null, StatusCode::InternalServerError->value, $e->getMessage());
        }

    }

    public function shopCheckout(Request $request)
    {
        $data = $request->validate([
            'currency' => ['required', 'in:USD,NGN'],
            'save_billing_information' => ['required', 'boolean'],
            'billing_information' => ['required', 'array'],
            'billing_information.first_name' => ['required', 'string', 'max:191'],
            'billing_information.last_name' => ['required', 'string', 'max:191'],
            'billing_information.email' => ['required', 'email', 'max:191'],
            'billing_information.apartment' => ['required', 'string', 'max:191'],
            'billing_information.city' => ['required', 'string', 'max:191'],
            'billing_information.country' => ['required', 'string', 'max:191'],
            'billing_information.phone' => ['required', 'string', 'max:191'],
            'items' => ['required', 'min:1', 'array'],
            'items.*.product_id' => ['required', 'string', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:1'],
        ]);
        $productIds = array_column($data['items'], 'product_id');

        try {
            DB::beginTransaction();
            $hasUnavailable = $this->hasUnavailableProduct($productIds);
            $user = $request->user();

            if ($hasUnavailable) {
                return $this->failed(null, StatusCode::BadRequest->value, 'Product is unavailable');
            }
            $invoiceItems = [];
            $amount = 0;

            if ($data['save_billing_information']) {
                $this->saveBillingInformation($user, $data['billing_information']);
            }

            foreach ($data['items'] as $item) {
                $product = Product::find($item['product_id']);
                $price = collect(sanitizedJsonDecode($product->price));
                $amountVal = (float)$price->where('currency', $data['currency'])->first()->amount;
                $amount += $amountVal * $item['quantity'];
                $invoiceItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $amountVal,
                    'product_type' => 'shop',
                    'currency' => $data['currency'],
                ];
            }
            $shippingFee = $this->getShippingFee($data['currency']);
            $totalAmount = $amount + $shippingFee;
            $trxId = Str::uuid();

            $invoice = Invoice::create([
                'amount' => $totalAmount,
                'status' => 'pending',
                'shipping_fee' => $shippingFee,
                'user_id' => $user->id,
                'currency' => $data['currency'],
                'billing_information' => json_encode($data['billing_information']),
                'trx_id' => $trxId,
            ]);

            $invoice->items()->createMany($invoiceItems);
            $paymentUrl = $this->generatePaymentLink($invoice);
            DB::commit();

            Mail::to($user->email)->send(new InvoiceMail($invoice));

            return $this->success(['payment_url' => $paymentUrl], 'Invoice created successfully.');
        } catch (\Throwable|\Exception $e) {
            DB::rollBack();
            return $this->failed(null, StatusCode::InternalServerError->value, "Payment failed");
        }
    }


    private function hasUnavailableProduct(array $products): bool
    {
        return Product::whereIn('id', $products)->where('available_quantity', '<', 1)->exists();
    }


    private function hasUnavailableTickets(array $tickets): bool
    {
        return EventTicket::whereIn('id', $tickets)->where('status', 'unavailable')->exists();
    }


    private function getShippingFee(string $currency): float
    {
        $fees = ShippingFee::first();

        if (!$fees) throw new \Exception("Shipping fee is not found");

        return (float)collect(json_decode($fees->fees, true))->where('currency', $currency)->first()['amount'];

    }

    private function saveBillingInformation(User $user, $billingInformation)
    {
        $user->billingInformation()->updateOrCreate([], $billingInformation);
    }

    private function generatePaymentLink(Invoice $invoice)
    {

        $amount = $invoice->amount;
        if ($invoice->currency === 'USD') {
            $stripe = $this->stripe();
            $session = $stripe->checkout->sessions->create([

                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => 'Payment for order',
                            ],
                            'unit_amount' => $amount * 100,
                        ],
                        'quantity' => 1,
                    ],
                ],
                'metadata' => [
                    'reference' => $invoice->trx_id,
                ],
                'mode' => 'payment',
                'success_url' => config('app.url'),
                'cancel_url' => config('app.url'),
            ]);

            $invoice->update(['payment_url' => $session->url, 'payment_method' => 'stripe']);

            return $session->url;
        }

        $headers = [
            'Authorization' => 'Bearer ' . config('services.paystack.secret_key')
        ];
        $billingInfomation = json_decode($invoice->billing_information, true);
        $postData = [
            'email' => $billingInfomation['email'],
            'amount' => (string)($amount * 100),
            'reference' => $invoice->trx_id,
            'callback_url' => config('services.paystack.callback_url'),
        ];

        $postUrl = config('services.paystack.url') . '/transaction/initialize';
        $response = Http::withHeaders($headers)
            ->post($postUrl, $postData);
        if (!$response->successful()) {
            throw new \Exception($response->json()['message']);
        }

        $paymentUrl = $response->json()['data']['authorization_url'];
        $invoice->update(['payment_url' => $paymentUrl, 'payment_method' => 'paystack']);

        return $paymentUrl;


    }
}
