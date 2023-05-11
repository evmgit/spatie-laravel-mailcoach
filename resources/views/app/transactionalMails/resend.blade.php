<x-mailcoach::card>
    @if($transactionalMail->opens->count())
        <x-mailcoach::warning>{{ __mc('This mail has already been opened, are you sure you want to resend it?') }}</x-mailcoach::warning>
    @else
        <x-mailcoach::info>{{ __mc('This mail hasn\'t been opened yet.') }}</x-mailcoach::info>
    @endif
    <x-mailcoach::form-buttons>
    <x-mailcoach::button :label="__mc('Resend')" class="mt-4 button" wire:click.prevent="resend" />
    </x-mailcoach::form-buttons>
</x-mailcoach::card>
