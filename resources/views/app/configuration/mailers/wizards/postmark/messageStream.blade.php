<div wire:init="loadStreams">
    @include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')

    <x-mailcoach::card>
        <form class="form-grid" wire:submit.prevent="submit">
            @if ($streamsLoaded)
                <x-mailcoach::select-field
                    wire:model="streamId"
                    :label="__mc('Message Stream')"
                    :options="$messageStreams"
                    :disabled="!count($messageStreams)"
                    :placeholder="__mc('Select a message stream')"
                />
            @else
                {{ __mc('Loading message streams...') }}
            @endif

            <x-mailcoach::form-buttons>
                <x-mailcoach::button :label="__mc('Save')" :disabled="!count($messageStreams)" />
        </x-mailcoach::form-buttons>
        </form>
    </x-mailcoach::card>
</div>
