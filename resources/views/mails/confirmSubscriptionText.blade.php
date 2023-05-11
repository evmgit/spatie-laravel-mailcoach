{{ __mc('Hey') }},

{{ __mc('You are almost subscribed to the list **:emailListName**.', ['emailListName'=>$subscriber->emailList->name]) }}

{{ __mc('Prove it is really you by pressing the button below') }}.

<a href="{{ $confirmationUrl }}">
    {{ __mc('Confirm subscription') }}
</a>

{{ $confirmationUrl }}

