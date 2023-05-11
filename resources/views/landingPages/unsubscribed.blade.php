@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __mc('Unsubscribed')])

@section('landing')
    <p>
        {{ __mc('Sorry to see you go.') }}
    </p>
    <p class="mt-4">
        {!! __mc('You have been unsubscribed from list <strong class="font-semibold">:emailListName</strong>.', ['emailListName' => $subscriber->emailList->name]) !!}
    </p>
@endsection
