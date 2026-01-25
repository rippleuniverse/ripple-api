@extends('layouts.emails.user', [
    'greetings' => 'Subscription Confirmed'
])

@section('content')
    <p>You have become a subscriber of the Ripple Universe mailing list.</p>
    <p>You have now been hooked to the beat of the Creative Tech Lab. We understand that your inbox is very important
        and hence we will not waste it. That is what you are going to find in your mailbox:</p>
    <ul>
        <li><b>Emerging Tech:</b> Info breaks of the newest inventions of Creative AI.</li>
        <li><b>Culture & Code:</b> Narratives of the convergence of technology and art.</li>
        <li><b>Lab News:</b> First to our new structured programs, masterclasses, and gallery events.</li>
    </ul>
    <p><i>Stay curious, </i> <br> <i>The Ripple Universe Team</i></p>
@endsection
