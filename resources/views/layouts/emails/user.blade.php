<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        p {
            font-size: 14px;
        }
    </style>
</head>

<body style="padding: 2rem">
<div style="margin-left: auto; margin-right: auto; width: max-content;">
    <img src="{{ asset('images/logo.png') }}" width="70px" alt="{{ config('app.name') }}">
</div>
<div style="margin-top: 1rem;">
    <h3 style="text-align: center; font-size: 1.5rem; margin: 0;">
        {{ $greetings ?? "" }}
    </h3>
    <p style="text-align: center; font-size: 1.5rem; margin: 0;">
        {{ $subtitle ?? "" }}
    </p>
</div>

<div style="background-color: #FFF8F8; border-radius: 0.5rem; margin-top: 2rem;">

    <div style="margin-left: auto; margin-right: auto; padding: 1rem">
        @yield('content')
    </div>

</div>

<footer>
    <p>
        <small>The Ripple Universe Team Preparing the next generation of innovators.</small>
    </p>
    <div style="margin-left: auto; margin-right: auto; padding: 1rem; width: max-content">
        <!-- <div style="margin-left: auto; margin-right: auto; text-align: center;">
                <a href="https://facebook.com" target="_blank" style="text-decoration: none">
                    <img src="{{ asset('images/facebook.png') }}" width="30px" height="30px" alt="Facebook">
                </a>
                <a href="https://facebook.com" target="_blank" style="text-decoration: none">
                    <img src="{{ asset('images/twitter.png') }}" width="30px" height="30px" alt="Facebook">
                </a>
                <a href="https://facebook.com" target="_blank" style="text-decoration: none">
                    <img src="{{ asset('images/linkedin.png') }}" width="30px" height="30px" alt="Facebook">
                </a>
                <a href="https://facebook.com" target="_blank" style="text-decoration: none">
                    <img src="{{ asset('images/instagram.png') }}" width="30px" height="30px" alt="Facebook">
                </a>
            </div> -->
        <div style="text-align: center;">
            @php
                $year = date('Y');
            @endphp
            <p style="margin: 0.4rem;">
                Copyright &copy; {{ $year }}
            </p>
            <h4 style="margin: 0.4rem;">{{ config('app.name') }}</h4>
            <p style="margin: 0.4rem;">
                All rights reserved.
                by {{ config('app.name') }}
            </p>
        </div>
    </div>
</footer>

</body>

</html>
