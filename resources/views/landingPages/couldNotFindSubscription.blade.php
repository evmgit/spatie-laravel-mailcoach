@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __mc('Could not find subscription')])

@section('landing')
    <p>
        {{ __mc('We could not find your subscription to this list.') }}
    </p>
    <p class="mt-4">
        {{ __mc('The link you used seems invalid.') }}
    </p>
@endsection
