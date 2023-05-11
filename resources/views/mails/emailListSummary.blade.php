@component('mailcoach::mails.layout.message')
{{ __mc('Hi') }},

{{ __mc("Here's what's been happening last week at your list **:emailListName** since :startDate", ['emailListName'=>$emailList->name, 'startDate'=>$summaryStartDateTime->toMailcoachFormat()]) }}.

@component('mailcoach::mails.layout.panel')
- {{ __mc('New subscriptions') }}: <strong>{{ number_format($summary['total_number_of_subscribers_gained']) }}</strong>
- {{ __mc('Unsubscribes') }}: <strong>{{ number_format($summary['total_number_of_unsubscribes_gained']) }}</strong>
- {{ __mc('Total number of subscribers') }}: <strong>{{ number_format($summary['total_number_of_subscribers']) }}</strong>
@endcomponent

@component('mailcoach::mails.layout.button', ['url' => $emailListUrl])
{{ __mc('View list') }}
@endcomponent

@component('mailcoach::mails.layout.subcopy')
[{{ __mc('Edit notification settings') }}]({{ $settingsUrl }})
@endcomponent
@endcomponent
