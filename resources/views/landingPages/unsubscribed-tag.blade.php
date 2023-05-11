@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __mc('Unsubscribed')])

@section('landing')
    <p>
        {{ __mc('Sorry to see you go.') }}
    </p>
    <p class="mt-4">
        {!! __mc('You have been unsubscribed from list <strong class="font-semibold">:emailListName</strong>\'s tag :tag.', ['emailListName' => $subscriber->emailList->name, 'tag' => $tag]) !!}
    </p>
@endsection
