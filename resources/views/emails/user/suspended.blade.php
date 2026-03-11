@extends('layouts.emails.user', [
    'greetings' => 'Hi ' . $user->full_name . ',',
    'subtitle' => 'Your account has been suspended'
])

@section('content')
    <p>This is an automated message notifying you that your account has been suspended. You have been restricted from
        accessing all
        available services via Ripple's website:
        <a href="https://rippleuniverse.org" target="_blank">https://rippleuniverse.org</a>
    </p>
    @if($reason)
        <h3>Reason:</h3>
        <p>{{$reason}}</p>
    @endif
    <p>If you have any questions or inquiries, please reach out to us via: <a
            style="text-decoration: underline;font-weight: 600" href="mailto:contact@rippleuniverse.org">contact@rippleuniverse.org</a>
    </p>
@endsection
