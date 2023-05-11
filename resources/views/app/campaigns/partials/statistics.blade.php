<div class="grid grid-cols-3 gap-6 justify-start items-end max-w-xl">
    @if ($campaign->open_count)
        <x-mailcoach::statistic :href="route('mailcoach.campaigns.opens', $campaign)" class="col-start-1"
                     numClass="text-4xl font-semibold" :stat="number_format($campaign->unique_open_count)" :label="__mc('Unique Opens')"/>
        <x-mailcoach::statistic :stat="number_format($campaign->open_count)" :label="__mc('Opens')"/>
        <x-mailcoach::statistic :stat="$campaign->open_rate / 100" :label="__mc('Open Rate')" suffix="%"/>
    @else
        <div class="col-start-1 col-span-3">
            <div class="text-4xl font-semibold">–</div>
            <div class="text-sm">{{ __mc('No opens tracked') }}</div>
        </div>
    @endif

    @if($campaign->click_count)
        <x-mailcoach::statistic :href="route('mailcoach.campaigns.clicks', $campaign)" class="col-start-1"
                     numClass="text-4xl font-semibold" :stat="number_format($campaign->unique_click_count)" :label="__mc('Unique Clicks')"/>
        <x-mailcoach::statistic :stat="number_format($campaign->click_count)" :label="__mc('Clicks')"/>
        <x-mailcoach::statistic :stat="$campaign->click_rate / 100" :label="__mc('Click Rate')" suffix="%"/>
    @else
        <div class="col-start-1 col-span-3">
            <div class="text-4xl font-semibold">–</div>
            <div class="text-sm">{{ __mc('No clicks tracked') }}</div>
        </div>
    @endif

    <x-mailcoach::statistic :href="route('mailcoach.campaigns.unsubscribes', $campaign)" numClass="text-4xl font-semibold"
                 :stat="number_format($campaign->unsubscribe_count)" :label="__mc('Unsubscribes')"/>
    <x-mailcoach::statistic :stat="$campaign->unsubscribe_rate / 100" :label="__mc('Unsubscribe Rate')" suffix="%"/>

    <x-mailcoach::statistic :href="route('mailcoach.campaigns.outbox', $campaign) . '?filter[type]=bounced'"
                 class="col-start-1" numClass="text-4xl font-semibold" :stat="number_format($campaign->bounce_count)"
                 :label="__mc('Bounces')"/>
    <x-mailcoach::statistic :stat="$campaign->bounce_rate / 100" :label="__mc('Bounce Rate')" suffix="%"/>
</div>
