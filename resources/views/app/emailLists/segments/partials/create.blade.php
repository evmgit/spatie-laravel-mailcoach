<form
    class="form-grid"
    wire:submit.prevent="saveSegment"
    @keydown.prevent.window.cmd.s="$wire.call('saveSegment')"
    @keydown.prevent.window.ctrl.s="$wire.call('saveSegment')"
    method="POST"
>
    @csrf
    <x-mailcoach::text-field :label="__mc('Name')" name="name" wire:model.lazy="name" required />

    <x-mailcoach::form-buttons>
        <x-mailcoach::button :label="__mc('Create segment')" />

        <button type="button" class="button-cancel" x-on:click="$store.modals.close('create-segment')">
            {{ __mc('Cancel') }}
        </button>
    </x-mailcoach::form-buttons>
</form>
