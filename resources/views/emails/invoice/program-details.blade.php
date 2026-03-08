@extends('layouts.emails.user', [
    'greetings' => 'Hi ' . $invoice->user->full_name . ',',
    'subtitle' => "Program details - {$program->name}"
])

@section('content')
    <h3>Payment Details:</h3>
    <ul>
        <li><b>Amount:</b> {{currencyFormat($invoice->amount, $invoice->currency)}}</li>
        <li><b>Discount:</b> {{currencyFormat($invoice->discount, $invoice->currency)}}</li>
        <li><b>Shipping fee:</b> {{currencyFormat($invoice->shipping_fee, $invoice->currency)}}</li>
        <li><b>Payment method: </b>{{$invoice->payment_method}}</li>
        <li><b>Payment status: </b>{{$invoice->status}}</li>
    </ul>
    <br>

    <img style="width: 5rem;height: 5rem;object-fit: cover;border-radius: 0.2rem" alt="{{$program->name}}"
         src="{{asset('storage/' . $program->featured_image)}}"/>
    <h2>{{$program->name}}</h2>
@endsection
