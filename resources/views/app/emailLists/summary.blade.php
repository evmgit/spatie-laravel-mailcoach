<div class="card-grid" wire:init="loadData">
<x-mailcoach::card>
    <div class="flex gap-4 items-center mb-8">
        <x-mailcoach::date-field
            min-date=""
            max-date="{{ $end }}"
            position="auto"
            name="start"
            wire:model="start"
            label="{{ __mc('From') }}"
            class="flex-row gap-0"
            inputClass="w-32"
        />
        <x-mailcoach::date-field
            min-date="{{ $start }}"
            max-date="{{ now()->format('Y-m-d') }}"
            position="auto"
            name="end"
            wire:model="end"
            label="{{ __mc('To') }}"
            class="flex-row gap-0"
            inputClass="w-32"
        />
    </div>
    @if ($readyToLoad)
        <div x-data="emailListStatisticsChart" x-init="renderChart({
            labels: @js($stats->pluck('label')->values()->toArray()),
            subscribers: @js($stats->pluck('subscribers')->values()->toArray()),
            subscribes: @js($stats->pluck('subscribes')->values()->toArray()),
            unsubscribes: @js($stats->pluck('unsubscribes')->values()->toArray()),
        })">
            <canvas id="chart" style="position: relative; max-height:300px; width:100%; max-width: 100%;"></canvas>
            <div class="mt-4 text-right">
                <small class="text-gray-500 text-sm">{{ __mc('You can drag the chart to zoom.') }}</small>
                <a x-show="zoomed" x-cloak class="text-gray-500 text-sm underline" href="#" x-on:click.prevent="resetZoom">Reset zoom</a>
            </div>
        </div>
    @else
        <div>
            {{ __mc('Loading...') }}
        </div>
    @endif
</x-mailcoach::card>

<x-mailcoach::card>
    <h2 class="markup-h2 mb-0">
        {{ __mc('Totals') }}
    </h2>

    <div class="grid grid-cols-4 gap-6 justify-start md:items-end">
        <x-mailcoach::statistic :href="route('mailcoach.emailLists.subscribers', $emailList)" class="col-start-1"
                                numClass="text-4xl font-semibold" :stat="number_format($totalSubscriptionsCount)" :label="__mc('Subscribers')"/>
        <x-mailcoach::statistic :href="route('mailcoach.emailLists.subscribers', $emailList)"
                                numClass="text-4xl font-semibold" :stat="number_format($totalSubscriptionsCount - $startSubscriptionsCount)" :label="__mc('Subscribers <small>(:daterange days)</small>', ['daterange' => \Illuminate\Support\Facades\Date::parse($start)->diffInDays($end) + 1])"/>
        <x-mailcoach::statistic :stat="$growthRate" :label="__mc('Growth Rate')" suffix="%"/>
        <div></div>
        <x-mailcoach::statistic :href="route('mailcoach.emailLists.subscribers', $emailList) . '?filter[status]=unsubscribed'" class="col-start-1"
                                numClass="text-4xl font-semibold" :stat="number_format($totalUnsubscribeCount)" :label="__mc('Unsubscribes')"/>
        <x-mailcoach::statistic :href="route('mailcoach.emailLists.subscribers', $emailList)  . '?filter[status]=unsubscribed'"
                                numClass="text-4xl font-semibold" :stat="number_format($startUnsubscribeCount)" :label="__mc('Unsubscribes <small>(:daterange days)</small>', ['daterange' => \Illuminate\Support\Facades\Date::parse($start)->diffInDays($end) + 1])"/>
        <x-mailcoach::statistic :stat="$churnRate" :label="__mc('Churn Rate')" suffix="%"/>
        <div></div>
        <x-mailcoach::statistic :stat="number_format($averageOpenRate, 2)" :label="__mc('Average Open Rate')" suffix="%"/>
        <x-mailcoach::statistic :stat="number_format($averageClickRate, 2)" :label="__mc('Average Click Rate')" suffix="%"/>
        <x-mailcoach::statistic :stat="number_format($averageUnsubscribeRate, 2)" :label="__mc('Average Unsubscribe Rate')" suffix="%"/>
        <x-mailcoach::statistic :stat="number_format($averageBounceRate, 2)" :label="__mc('Average Bounce Rate')" suffix="%"/>
    </div>
</x-mailcoach::card>
</div>
