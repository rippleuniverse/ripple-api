<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div>
        {{-- <img src="{{asset('images/logo.png')}}" --}} {{-- alt="{{config('app.name')}}" --}} {{-- width="70px">--}}
    </div>
    <h1>
        {{$ticket->name}} for {{$ticket->event->title}}
    </h1>

    <p>Ticket details</p>
    <ul>
        <li>Name: {{$ticket->name}}</li>
        <li>Quantity: {{$item->quantity}}</li>
    </ul>
    <p><b>QR Code</b></p>
    <img src="{{ $qrCode }}" width="150" height="150">
</body>

</html>