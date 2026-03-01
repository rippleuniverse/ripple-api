<?php

namespace App\Http\Controllers\Webhook;

use App\Enums\Enums\StatusCode;
use App\Http\Controllers\Controller;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WebhookController extends Controller
{

    public const STRIPE_EVENTS = ['checkout.session.completed'];
    public const PAYSTACK_EVENTS = ['charge.success'];

    public function paystack(Request $request)
    {


        if ($request->header('X-Paystack-Signature') !== hash_hmac('sha512', $request->getContent(), config('services.paystack.secret_key'))) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $event = $request->event;

        if (!in_array($event, $this::PAYSTACK_EVENTS)) {
            return response()->json(['message' => 'Invalid event'], 400);
        }

        $data = $request->data;
        $reference = $data['reference'];
        $invoice = Invoice::where('trx_id', $reference)->first();
        $amount = (float)$data['amount'];

        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        $invoiceAmount = (float)$invoice->amount * 100;

        if ($invoiceAmount !== $amount) {
            return response()->json(['message' => 'Invalid amount'], 400);
        }

        $invoice->update([
            'status' => 'paid',
        ]);


        $invoice->items->each(function ($item) {
            if ($item->product_type === 'shop') {
                $product = Product::find($item->product_id);
                $product->decrement('available_quantity', $item->quantity);
            }
        });

        Mail::to($invoice->user->email)->send(new InvoiceMail($invoice));


        return response(null, StatusCode::Success->value);
    }


    public function stripe(Request $request)
    {

        try {
            $payload = @file_get_contents('php://input');
            $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
            $event = null;

            try {
                $event = \Stripe\Webhook::constructEvent(
                    $payload, $sig_header, config('services.stripe.webhook_secret')
                );
            } catch (\UnexpectedValueException $e) {
                return response()->json(['message' => 'Invalid payload'], 400);
            } catch (\Stripe\Exception\SignatureVerificationException $e) {
                return response()->json(['message' => 'Invalid signature'], 400);
            }

            if (!in_array($event->type, $this::STRIPE_EVENTS)) {
                return response()->json(['message' => 'Invalid event'], 400);
            }

            $data = $event->data->object;
            $reference = $data['metadata']['reference'];

            $invoice = Invoice::where('trx_id', $reference)->first();
            $amount = (float)($data['amount_total'] / 100);


            if (!$invoice) {
                return response()->json(['message' => 'Invoice not found'], 404);
            }
            $invoiceAmount = (float)$invoice->amount;

            if ($invoiceAmount !== $amount) {
                return response()->json(['message' => 'Invalid amount'], 400);
            }

            $invoice->update([
                'status' => 'paid',
            ]);

            $invoice->items->each(function ($item) {
                if ($item->product_type === 'shop') {
                    $product = Product::find($item->product_id);
                    $product->decrement('available_quantity', $item->quantity);
                }
            });

            Mail::to($invoice->user->email)->send(new InvoiceMail($invoice));

            return response(null, StatusCode::Success->value);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }


    }
}
