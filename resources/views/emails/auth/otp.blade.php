@extends('layouts.emails.user', [
    'greetings' => 'Hi ' . $otp->user->full_name . ',',
    'subtitle' => 'One Time Password (OTP) for your account'
])

@section('content')

    @if ($otp->type === 'email_verification')
        <p>
            Please use the following OTP to verify your email address.
        </p>
        <h3>
            {{ $otp->code }}
        </h3>
        <p>Please do not share this OTP with anyone as it poses security risks.</p>

    @else
        <p>
            Please use the following OTP to reset your password.
        </p>
        <h3 style="text-align:center">
            {{ $otp->code }}
        </h3>
        <p>Please do not share this OTP with anyone as it poses security risks.</p>

    @endif

    <p>
        <strong>NOTE: </strong> This OTP will expire in 30 minutes from now.
    </p>

@endsection
