<x-mailcoach::layout
        :originTitle="$originTitle ?? $automation->name"
        :originHref="$originHref ?? null"
        :title="$title ?? null"
>
    <x-slot name="nav">
        <x-mailcoach::navigation :title="$automation->name">
            <x-mailcoach::navigation-item :href="route('mailcoach.automations.settings', $automation)" data-dirty-warn>
                {{ __mc('Settings') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.automations.actions', $automation)" data-dirty-warn>
                {{ __mc('Actions') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item
                    x-data="{ running: {{ $automation->status === \Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus::Started ? 'true' : 'false' }} }"
                    @automation-started.window="running = true"
                    @automation-paused.window="running = false"
                    :href="route('mailcoach.automations.run', $automation)"
                    data-dirty-warn

            >
                <span class="flex items-baseline gap-2">
                <span>{{ __mc('Run')}} </span>
                    <i class="opacity-50" :class="[running ? 'fas fa-spin fa-sync ' : 'fa fa-pause']"></i>
                </span>
            </x-mailcoach::navigation-item>
        </x-mailcoach::navigation>
    </x-slot>

    {{ $slot }}
</x-mailcoach::layout>
