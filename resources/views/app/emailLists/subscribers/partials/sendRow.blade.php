<tr>
    <td class="markup-links">
        @if ($row->concernsCampaign() && $row->campaign)
            <a class="break-words" href="{{ route('mailcoach.campaigns.summary', $row->campaign) }}">
                {{ $row->campaign->name }}
            </a>
        @elseif ($row->concernsAutomationMail() && $row->automationMail)
            <a class="break-words" href="{{ route('mailcoach.automations.mails.summary', $row->automationMail) }}">
                {{ $row->automationMail->name }}
            </a>
        @elseif ($row->concernsTransactionalMail() && $row->transactionalMailLogItem)
            <a class="break-words" href="{{ route('mailcoach.transactionalMails.show', $row->transactionalMailLogItem) }}">
                {{ $row->transactionalMailLogItem->name }}
            </a>
        @endif
    </td>
    @if ($row->failed_at)
        <td class="text-right" colspan="3">
            <span class="tag bg-orange-100 text-gray-800">{{ __mc('Failed') }}</span>
            <span class="text-sm">{{ $row->failure_reason }}</span>
            <x-mailcoach::button-secondary wire:click.prevent="retry('{{ $row->id }}')" class="text-sm" :label="__mc('Retry')" />
        </td>
    @else
        <td class="td-numeric">{{ $row->opens_count }}</td>
        <td class="td-numeric">{{ $row->clicks_count }}</td>
        <td class="td-numeric hidden | xl:table-cell">
            {{ $row->sent_at?->toMailcoachFormat() }}
        </td>
    @endif
</tr>
