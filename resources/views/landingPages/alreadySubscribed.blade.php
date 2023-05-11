@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __mc('Already subscribed')])

@section('landing')
    <p>
        {{ __mc('You are a real fan!') }}
    </p>
    <p class="mt-4">
        {{ __mc('You were already subscribed to this list.') }}
    </p>
@endsection
