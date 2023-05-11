<form
    class="form-grid"
    wire:submit.prevent="saveAutomation"
    @keydown.prevent.window.cmd.s="$wire.call('saveAutomation')"
    @keydown.prevent.window.ctrl.s="$wire.call('saveAutomation')"
    method="POST"
>
    @csrf

    <x-mailcoach::text-field
        :label="__mc('Name')"
        name="name"
        wire:model.lazy="name"
        :placeholder="__mc('Automation name')"
        required
    />

    @if (count($emailListOptions))
        <x-mailcoach::select-field
            :label="__mc('Email list')"
            :options="$emailListOptions"
            wire:model.lazy="email_list_id"
            name="email_list_id"
            required
        />

        <x-mailcoach::form-buttons>
            <x-mailcoach::button :label="__mc('Create automation')"/>
            <x-mailcoach::button-cancel :label="__mc('Cancel')" x-on:click="$store.modals.close('create-automation')" />
        </x-mailcoach::form-buttons>
    @else
        <p>{!! __mc('You\'ll need to create a list first. <a class="link" href=":url">Create one here</a>', [
            'url' => route('mailcoach.emailLists') . '#create-list'
        ]) !!}</p>
        <x-mailcoach::form-buttons>
            <x-mailcoach::button-cancel x-on:click="$store.modals.close('create-automation')" />
        </x-mailcoach::form-buttons>
    @endif

</form>
