<x-mailcoach::layout
    :originTitle="$originTitle ?? $template->name"
    :originHref="$originHref ?? null"
    :title="$title ?? null"
>
    <x-slot name="nav">
        <x-mailcoach::navigation :title="$template->name">
            <x-mailcoach::navigation-item :href="route('mailcoach.transactionalMails.templates.edit', $template)" data-dirty-warn>
                {{ __mc('Content') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.transactionalMails.templates.settings', $template)"
                                        data-dirty-warn>
                {{ __mc('Settings') }}
            </x-mailcoach::navigation-item>
        </x-mailcoach::navigation>
    </x-slot>

    {{ $slot }}
</x-mailcoach::layout>
