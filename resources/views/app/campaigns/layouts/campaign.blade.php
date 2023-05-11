<x-mailcoach::layout
    :originTitle="$originTitle ?? $campaign->name"
    :originHref="$originHref ?? null"
    :title="$title ?? null"
>
    <x-slot name="nav">
        <x-mailcoach::navigation :title="$campaign->name">
            @if ($campaign->isSendingOrSent() || $campaign->isCancelled())
                <x-mailcoach::navigation-group :title="__mc('Performance')" :href="route('mailcoach.campaigns.summary', $campaign)" data-dirty-warn>
                    <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.opens', $campaign)" data-dirty-warn>
                        {{ __mc('Opens') }}
                    </x-mailcoach::navigation-item>
                    <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.clicks', $campaign)" data-dirty-warn>
                        {{ __mc('Clicks') }}
                    </x-mailcoach::navigation-item>
                    <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.unsubscribes', $campaign)" data-dirty-warn>
                        {{ __mc('Unsubscribes') }}
                    </x-mailcoach::navigation-item>

                    <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.outbox', $campaign)" data-dirty-warn>
                        {{ __mc('Outbox') }}
                    </x-mailcoach::navigation-item>
                </x-mailcoach::navigation-group>
            @endif

            <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.settings', $campaign)" data-dirty-warn>
                {{ __mc('Settings') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.content', $campaign)" data-dirty-warn>
                {{ __mc('Content') }}
            </x-mailcoach::navigation-item>

            @if (! $campaign->isSendingOrSent() && ! $campaign->isCancelled())
                <x-mailcoach::navigation-item :href="route('mailcoach.campaigns.delivery', $campaign)" data-dirty-warn>
                    {{ __mc('Send') }}
                </x-mailcoach::navigation-item>
            @endif

        </x-mailcoach::navigation>
    </x-slot>

    {{ $slot }}
</x-mailcoach::layout>
