@extends('layouts.emails.user', [
    'greetings' => 'Your admin account has been created',
])

@section('content')
    <p>Hi {{$user->full_name}},</p>
    <p>Welcome to the Ripple Team! Your admin account has been created, please login to the website: <a
            href="https://admin.rippleuniverse.org/signin" style="text-decoration: underline">https://admin.rippleuniverse.org/signin</a>
        then
        complete your email verification process.
    </p>
    <h4>Login details:</h4>
    <ul>
        <li><b>Email address: </b>{{$user->email}}</li>
        <li><b>Password: </b>{{$password}}</li>
    </ul>
    <p><b>NOTE: </b><i>You may change the password after you login</i></p>
    <p>If you have any questions or inquiries, please reach out to us via: <a
            style="text-decoration: underline;font-weight: 600" href="mailto:contact@rippleuniverse.org">contact@rippleuniverse.org</a>
    </p>
@endsection
