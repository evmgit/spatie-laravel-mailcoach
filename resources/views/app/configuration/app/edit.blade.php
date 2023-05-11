<form
    class="card-grid"
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
    method="POST"
    data-cloak
>
    @method('PUT')
    @csrf

    <x-mailcoach::card>
    <x-mailcoach::text-field name="name" id="name" wire:model="name" :label="__mc('App name')" />
    <x-mailcoach::text-field name="url" id="url" wire:model="url" :label="__mc('App url')" />

    <x-mailcoach::select-field
        :label="__mc('Timezone')"
        name="timezone"
        wire:model="timezone"
        :options="$timeZones"
    />

    <x-mailcoach::text-field name="from_address" id="from_address" wire:model="from_address" :label="__mc('Default email from address')" />

    <x-mailcoach::form-buttons>
        <x-mailcoach::button :label="__mc('Save')"/>
    </x-mailcoach::form-buttons>
    </x-mailcoach::card>
</form>
