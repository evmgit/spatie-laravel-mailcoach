<x-mailcoach::layout
    :originTitle="$originTitle ?? $transactionalMail->subject"
    :originHref="$originHref ?? null"
    :title="$title ?? null"
>

     <x-slot name="nav">
        <x-mailcoach::navigation :title="$transactionalMail->subject">
            <x-mailcoach::navigation-item :href="route('mailcoach.transactionalMails.show', $transactionalMail)">
                {{ __mc('Content') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.transactionalMails.performance', $transactionalMail)">
                {{ __mc('Performance') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.transactionalMails.resend', $transactionalMail)">
                {{ __mc('Resend') }}
            </x-mailcoach::navigation-item>
        </x-mailcoach::navigation>
    </x-slot>

    {{ $slot }}
</x-mailcoach::layout>
