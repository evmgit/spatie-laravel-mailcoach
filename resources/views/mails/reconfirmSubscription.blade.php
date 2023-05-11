@component('mailcoach::mails.layout.message')
{{ __mc('Hey') }},

{{ __mc('It seems like you haven’t read our emails in a while.') }}

{{ __mc('Do you want to stay subscribed to our email list **:emailListName**?', ['emailListName'=>$subscriber->emailList->name]) }}

@component('mailcoach::mails.layout.button', ['url' => $confirmationUrl])
{{ __mc('Stay subscribed') }}
@endcomponent

@endcomponent
