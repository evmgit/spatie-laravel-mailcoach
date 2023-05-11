@if($status === \Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus::Draft)
    @if($campaign->scheduled_at)
        <x-mailcoach::rounded-icon minimal size="md" title="{{ __mc('Scheduled') }}" type="warning" icon="far fa-clock"/>
    @else
        <x-mailcoach::rounded-icon minimal size="md" title="{{ __mc('Draft') }}" type="neutral" icon="far fa-pen"/>
    @endif
@elseif ($status === \Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus::Sent)
    <x-mailcoach::rounded-icon minimal size="md" title="{{ __mc('Sent') }}" type="success" icon="far fa-check"/>
@elseif ($status === \Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus::Sending)
    <x-mailcoach::rounded-icon minimal size="md" title="{{ __mc('Sending') }}" type="info" icon="far fa-sync fa-spin"/>
@elseif ($status === \Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus::Cancelled)
    <x-mailcoach::rounded-icon minimal size="md" title="{{ __mc('Cancelled') }}" type="error" icon="far fa-ban"/>
@endif
