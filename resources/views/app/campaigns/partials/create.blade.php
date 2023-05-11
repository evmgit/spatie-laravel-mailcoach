<form
    class="form-grid"
    wire:submit.prevent="saveCampaign"
    @keydown.prevent.window.cmd.s="$wire.call('saveCampaign')"
    @keydown.prevent.window.ctrl.s="$wire.call('saveCampaign')"
    method="POST"
>
    <x-mailcoach::text-field
        :label="__mc('Name')"
        wire:model.lazy="name"
        name="name"
        :placeholder="__mc('Newsletter #1')"
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

    @if(count($templateOptions) > 1)
        <x-mailcoach::select-field
            :label="__mc('Template')"
            :options="$templateOptions"
            wire:model.lazy="template_id"
            position="top"
            name="template_id"
        />
    @endif

    <x-mailcoach::form-buttons>
        <x-mailcoach::button :label="__mc('Create campaign')" />
        <x-mailcoach::button-cancel x-on:click="$store.modals.close('create-campaign')" />
    </x-mailcoach::form-buttons>
    @else
        <p>{!! __mc('You\'ll need to create a list first. <a class="link" href=":url">Create one here</a>', [
            'url' => route('mailcoach.emailLists') . '#create-list'
        ]) !!}</p>
        <x-mailcoach::form-buttons>
            <x-mailcoach::button-cancel x-on:click="$store.modals.close('create-campaign')" />
        </x-mailcoach::form-buttons>
    @endif
</form>
