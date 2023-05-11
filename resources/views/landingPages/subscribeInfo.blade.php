@extends('mailcoach::landingPages.layouts.landingPage', [
    'title' => __mc('This endpoint requires a POST request'),
    'noIndex' => true,
])

@section('landing')
    <p>
        {{ __mc('Whoops!') }}
    </p>
    <p class="mt-4">
        {{ __mc('This endpoint requires a POST request. Make sure your subscribe form is doing a POST and not a GET request.') }}
    </p>
    <p class="mt-4">
        {!! __mc('Take a look <a class="text-blue-500 underline" href=":docsUrl">at the documentation</a> for more info.', [
            'docsUrl' => 'https://mailcoach.app/docs/v5/mailcoach/using-mailcoach/audience#content-onboarding',
        ]) !!}
    </p>
@endsection

