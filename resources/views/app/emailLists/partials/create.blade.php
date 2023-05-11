<form
    class="form-grid"
    wire:submit.prevent="saveList"
    @keydown.prevent.window.cmd.s="$wire.call('saveList')"
    @keydown.prevent.window.ctrl.s="$wire.call('saveList')"
    method="POST"
>
    <x-mailcoach::text-field :label="__mc('Name')"  wire:model.lazy="name" name="name" :placeholder="__mc('Subscribers')" required />
    <x-mailcoach::text-field :label="__mc('From email')" :placeholder="auth()->guard(config('mailcoach.guard'))->user()->email" wire:model.lazy="default_from_email" name="default_from_email" type="email" required />
    <x-mailcoach::text-field :label="__mc('From name')" :placeholder="auth()->guard(config('mailcoach.guard'))->user()->name" wire:model.lazy="default_from_name" name="default_from_name" />

    <x-mailcoach::form-buttons>
        <x-mailcoach::button :label="__mc('Create list')" />
        <button type="button" class="button-cancel"  x-on:click="$store.modals.close('create-list')">
            {{ __mc('Cancel') }}
        </button>
    </x-mailcoach::form-buttons>
</form>
