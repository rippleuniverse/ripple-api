@extends('layouts.emails.user', [
    'greetings' => "Application Received for ". $application->role->name
])

@section('content')
    <p> Hi {{$application->name}},</p>
    <p>Thanks to you throwing your hat in the ring.</p>
    <p>Your application has been received in the absence of the {{$application->role->name}} position
        at {{$application->role->company_name}}.</p>
    <p>
        We are not merely seeking workers in {{$application->role->company_name}}, but the future generations of the
        inventors, to assist us
        in the construction of the Creative Tech Lab of the Future. Thanks to your action, we understand that you are
        serious about the future of AI and technology.
    </p>
    <p>
        What happens next? Our team is also examining your information, portfolio and experience to determine whether
        you will fit in with our mission at hand.
    </p>
    <p>In case it is a match: We are going to contact you to have a chat.</p>
    <p>Otherwise: Your profile will be held in our talent pool until such other opportunities arise that would better
        fit.</p>
    <p>Meanwhile, you can come visit our Insights page and see what we have been working on or look at the future Events
        where you can find out how to connect with the community.</p>
    <p>
        Stay creative,
    </p>
    <p>The Ripple Talent Team</p>
@endsection
