@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __mc('Unsubscribed')])

@section('landing')
    <p class="mt-4">
        {!! __mc('Are you sure you want to unsubscribe from list <strong class="font-semibold">:emailListName</strong>?', ['emailListName' => $subscriber->emailList->name]) !!}
    </p>

    <div class="mt-4">
        <form method="POST">
            @csrf
            <button class="button bg-red-400 shadow" id="confirmationButton" type="submit">{{__mc('Unsubscribe') }}</button>
        </form>
    </div>

    @if (is_null($send) || $send->created_at->isBefore(now()->subMinutes(5)))
        <script>
            document.getElementById("confirmationButton").click();
        </script>
    @endif
@endsection
