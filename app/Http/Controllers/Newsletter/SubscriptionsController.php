<?php

namespace App\Http\Controllers\Newsletter;

use App\Http\Controllers\Controller;
use App\Mail\Newsletter\SubscribedMail;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SubscriptionsController extends Controller
{
    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'unique:newsletter_subscriptions,email']
        ]);

        NewsletterSubscription::create($data);
        Mail::to($data['email'])->send(new SubscribedMail());

        return $this->success(null, 'Subscribed to newsletter successfully.');
    }
}
