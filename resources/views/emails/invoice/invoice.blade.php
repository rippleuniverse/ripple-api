@extends('layouts.emails.user', [
    'greetings' => 'Hi ' . $invoice->user->full_name . ',',
    'subtitle' => 'Your invoice #'.$invoice->id.' is '.$invoice->status_title
])

@section('content')

    <p>Your Invoice <b># {{$invoice->id}}</b> has been updated</p>
    <h3>Payment Details:</h3>
    <ul>
        <li><b>Amount:</b> {{currencyFormat($invoice->amount, $invoice->currency)}}</li>
        <li><b>Shipping fee:</b> {{currencyFormat($invoice->shipping_fee, $invoice->currency)}}</li>
        <li><b>Payment method: </b>{{$invoice->payment_method}}</li>
        <li><b>Payment status: </b>{{$invoice->status}}</li>
    </ul>

    <h3>Billing Details:</h3>
    <ul>
        @php
            $info = sanitizedJsonDecode($invoice->billing_information, true);
        @endphp

        <li><b>First name:</b> {{$info['first_name']}}</li>
        <li><b>Last name:</b> {{$info['last_name']}}</li>
        <li><b>Email address:</b> {{$info['email']}}</li>
        <li><b>Phone:</b> {{$info['phone']}}</li>
        <li><b>Apartment:</b> {{$info['apartment']}}</li>
        <li><b>Phone:</b> {{$info['city']}}</li>
        <li><b>Country:</b> {{$info['country']}}</li>
    </ul>


    <h3>Product Details:</h3>
    @foreach($invoice->items as $item)

        <div>
            <img style="width: 5rem;height: 5rem;object-fit: cover;border-radius: 0.2rem" alt="{{$item->product_name}}"
                 src="{{asset('storage/' . $item->item->featured_image)}}"/>
            <ul>
                <li><b>Product: </b>{{$item->product_name}}</li>
                <li><b>Quantity: </b>{{$item->quantity}}</li>
                <li><b>Unit price: </b>{{currencyFormat($item->unit_price, $invoice->currency)}}</li>
                <li><b>Total: </b>{{currencyFormat($item->total, $invoice->currency)}}</li>
            </ul>
        </div>

    @endforeach

@endsection
