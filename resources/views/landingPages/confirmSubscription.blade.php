@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __mc('Confirm subscription')])

@section('landing')
    <p>
        {{ __mc('Hey, is that really you?') }}
    </p>
    <p class="mt-4">
        {{ __mc("We've sent you an email to confirm your subscription.") }}
    </p>
@endsection
