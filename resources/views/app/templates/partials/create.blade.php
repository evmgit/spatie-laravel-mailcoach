<form
    class="form-grid"
    wire:submit.prevent="saveTemplate"
    @keydown.prevent.window.cmd.s="$wire.call('saveTemplate')"
    @keydown.prevent.window.ctrl.s="$wire.call('saveTemplate')"
    method="POST"
>
    <x-mailcoach::text-field
        :label="__mc('Name')"
        name="name"
        :placeholder="__mc('Newsletter template')"
        wire:model.lazy="name"
        required
    />

    <x-mailcoach::form-buttons>
        <x-mailcoach::button :label="__mc('Create template')" />
        <x-mailcoach::button-cancel  x-on:click="$store.modals.close('create-template')" />
    </x-mailcoach::form-buttons>
</form>
