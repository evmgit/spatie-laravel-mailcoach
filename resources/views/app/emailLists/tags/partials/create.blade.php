<form
    class="form-grid"
    wire:submit.prevent="saveTag"
    @keydown.prevent.window.cmd.s="$wire.call('saveTag')"
    @keydown.prevent.window.ctrl.s="$wire.call('saveTag')"
    method="POST"
>
    @csrf

    <x-mailcoach::text-field :label="__mc('Name')" wire:model="name" name="name" required />

    <x-mailcoach::form-buttons>
        <x-mailcoach::button :label="__mc('Create tag')"/>
        <button type="button" class="button-cancel" x-on:click="$store.modals.close('create-tag')">
            {{ __mc('Cancel') }}
        </button>
    </x-mailcoach::form-buttons>
</form>
