<form
    class="form-grid"
    wire:submit.prevent="saveSubscriber"
    @keydown.prevent.window.cmd.s="$wire.call('saveSubscriber')"
    @keydown.prevent.window.ctrl.s="$wire.call('saveSubscriber')"
    method="POST"
>
    @csrf
    <x-mailcoach::text-field :label="__mc('Email')" wire:model.lazy="email" name="email" type="email" required />
    <x-mailcoach::text-field :label="__mc('First name')" wire:model.lazy="first_name" name="first_name" />
    <x-mailcoach::text-field :label="__mc('Last name')" wire:model.lazy="last_name" name="last_name" />

    <x-mailcoach::form-buttons>
        <x-mailcoach::button :label="__mc('Add subscriber')" />
        <button type="button" class="button-cancel" x-on:click="$store.modals.close('create-subscriber')">
            {{ __mc('Cancel') }}
        </button>
    </x-mailcoach::form-buttons>
</form>
