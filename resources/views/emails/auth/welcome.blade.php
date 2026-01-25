@extends('layouts.emails.user', [
    'greetings' => 'Welcome to Ripple Universe. Your adventure into Creative AI begins.',
])

@section('content')

    <p>Hi {{$user->full_name}}</p>
    <p>Welcome to Ripple Universe.</p>
    <p>
        You have just been recruited into the Creative Tech Lab of the Future. It is truly our pleasure to welcome you
        to our expanding portfolio of creators, technologists, and innovators that are defining the future.
    </p>
    <p>
        The creativity of the future is not only utilizing new tools, but comprehending them, destroying them, and
        creating superior versions. This is what you will find when the Universe heads you:
    </p>
    <ul>
        <li>
            <b>LEARN:</b> Study organized programs, classes and laboratories to assist you to break into innovative AI
            and
            technology.
        </li>
        <li>
            <b>EXPERIENCE:</b> Be immersed in our events, galleries, and masterclasses and learn to tie deep learning
            with culture.
        </li>
        <li>
            <b>CREATE:</b> This is where you can create, experiment and present your creation to the world.
        </li>
        <li>
            <b>CONNECT:</b> This universe is being created so that you can live during the era of AI. We are excited to
            know what you will produce.
        </li>
    </ul>

@endsection
