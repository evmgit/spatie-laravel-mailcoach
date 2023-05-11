@php($campaign ??= $row)
<tr @if($campaign->isSending()) id="campaign-row-{{ $campaign->id }}" wire:poll @endif>
    <td>
        @include('mailcoach::app.campaigns.partials.campaignStatusIcon', ['status' => $campaign->status])
    </td>
    <td class="markup-links">
        @if($campaign->isSent() || $campaign->isSending() || $campaign->isCancelled())
            <a href="{{ route('mailcoach.campaigns.summary', $campaign) }}">
                {{ $campaign->name }}
            </a>
        @elseif($campaign->isScheduled())
            <a href="{{ route('mailcoach.campaigns.delivery', $campaign) }}">
                {{ $campaign->name }}
            </a>
        @else
            <a href="{{ route('mailcoach.campaigns.content', $campaign) }}">
                {{ $campaign->name }}
            </a>
        @endif
        @if ($campaign->sends_with_errors_count)
            <div class="flex items-center text-orange-500 text-xs mt-1">
                <x-mailcoach::rounded-icon type="warning" icon="fas fa-info" class="mr-1" />
                {{ $campaign->sends_with_errors_count }} {{ __mc_choice('failed send|failed sends', $campaign->sends_with_errors_count) }}
            </div>
        @endif
    </td>
    <td class="markup-links table-cell">
        @if ($campaign->emailList)
        <a href="{{ route('mailcoach.emailLists.subscribers', $campaign->emailList) }}">
            {{ $campaign->emailList->name }}
        </a>
        @if($campaign->usesSegment())
            <div class="td-secondary-line">
                {{ $campaign->getSegment()->description() }}
            </div>
        @endif
        @else
            &ndash;
        @endif
    </td>
    <td class="td-numeric">
        @if ($campaign->isCancelled())
            {{ $campaign->sendsCount() ? number_format($campaign->sendsCount()) : '–' }}
        @else
            {{ number_format($campaign->sent_to_number_of_subscribers) ?: '–' }}
        @endif
    </td>
    <td class="td-numeric hidden | xl:table-cell">
        @if($campaign->open_rate)
            {{ number_format($campaign->unique_open_count) }}
            <div class="td-secondary-line">{{ $campaign->open_rate / 100 }}%</div>
        @else
            –
        @endif
    </td>
    <td class="td-numeric hidden | xl:table-cell">
        @if($campaign->click_rate)
            {{ number_format($campaign->unique_click_count) }}
            <div class="td-secondary-line">{{ $campaign->click_rate / 100 }}%</div>
        @else
            –
        @endif
    <td class="td-numeric hidden | xl:table-cell">
        @if($campaign->isSent())
            {{ optional($campaign->sent_at)->toMailcoachFormat() }}
        @elseif($campaign->isSending())
            {{ optional($campaign->updated_at)->toMailcoachFormat() }}
            <div class="td-secondary-line">
                {{ __mc('In progress') }}
            </div>
        @elseif($campaign->isScheduled())
            {{ optional($campaign->scheduled_at)->toMailcoachFormat() }}
            <div class="td-secondary-line">
                {{ __mc('Scheduled') }}
            </div>
        @else
            –
        @endif
    </td>

    <td class="td-action">
         <x-mailcoach::dropdown direction="left">
            <ul>
                <li>
                    <button wire:click.prevent="duplicateCampaign({{ $campaign->id }})">
                        <x-mailcoach::icon-label icon="fas fa-random" :text="__mc('Duplicate')" />
                    </button>
                </li>
                <li>
                    <x-mailcoach::confirm-button
                        :confirm-text="__mc('Are you sure you want to delete campaign :campaignName?', ['campaignName' => $campaign->name])"
                        onConfirm="() => $wire.deleteCampaign({{ $campaign->id }})"
                    >
                        <x-mailcoach::icon-label icon="far fa-trash-alt" :text="__mc('Delete')" :caution="true" />
                    </x-mailcoach::confirm-button>
                </li>
            </ul>
        </x-mailcoach::dropdown>
    </td>
</tr>
