@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __mc('Subscribed')])

@section('landing')
    <p>
        {{ __mc('Happy to have you!') }}
    </p>
    <p class="mt-4">
        @isset($subscriber)
            {!! __mc('You are now subscribed to the list <strong class="font-semibold">:emailListName</strong>.', ['emailListName' => $subscriber->emailList->name]) !!}
        @else
            {!! __mc('You are now subscribed to the list <strong class="font-semibold">our dummy mailing list</strong>.') !!}
        @endif
    </p>
@endsection

