@extends('layouts.emails.user', [
    'greetings' => 'Hi ' . $ticket->invoice->user?->full_name . ',',
    'subtitle' => "Purchase for {$ticket->ticket->name} was successful"
])

@section('content')
    <p>Your purchase for <b>{{$ticket->ticket?->name}}</b> for <b>{{$ticket->ticket->event->title}}</b> was successful
    </p>
    <h3>Ticket details:</h3>
    <img style="width: 5rem;height: 5rem;object-fit: cover;border-radius: 0.2rem"
         alt="{{$ticket->ticket->event->title}}"
         src="{{asset('storage/' . $ticket->ticket->event->featured_image)}}"/>
    <ul>
        <li><b>Event:</b> {{$ticket->ticket?->event->title}}</li>
        <li><b>Ticket name:</b> {{$ticket->ticket?->name}}</li>
        <li><b>Ticket No:</b> {{$ticket->uid}}</li>
        <li><b>Quantity:</b> {{$ticket->quantity}}</li>
        <li><b>Unit Price:</b> {{currencyFormat($ticket->unit_price, $ticket->invoice?->currency)}}</li>
        <li><b>Total Price:</b> {{currencyFormat($ticket->total, $ticket->invoice?->currency)}}</li>
    </ul>
    <p><b><i>NOTE: This email may be a proof for ticket possession</i></b></p>
@endsection
