<div>
    <x-mailcoach::card>
        <x-mailcoach::help>
            <p>{{ __mc('A template is a reusable layout that can be used as a starting point for your campaigns, automation emails or transactional mails.') }}</p>
            <p>{!! __mc('Create slots in your template by adding the name in triple brackets, for example: <code>[[[content]]]</code>. You can add as many slots as you like.') !!}</p>
            <span>{!! __mc('You can add a normal text field by appending <code>:text</code> to your placeholder, for example: <code>[[[preheader:text]]]</code>') !!}</span>
        </x-mailcoach::help>

        <form
            class="form-grid mt-6"
            wire:submit.prevent="save"
            @keydown.prevent.window.cmd.s="$wire.call('save')"
            @keydown.prevent.window.ctrl.s="$wire.call('save')"
            method="POST"
        >
            <x-mailcoach::text-field :label="__mc('Name')" name="template.name" wire:model="template.name" required />

            @livewire(\Livewire\Livewire::getAlias(config('mailcoach.template_editor')), [
                'model' => $template,
                'quiet' => true,
            ])
        </form>
    </x-mailcoach::card>

    <x-mailcoach::fieldset class="mt-6" card :legend="__mc('Usage in Mailcoach API')">
        <div>
            <x-mailcoach::help>
                {!! __mc('Whenever you need to specify a <code>:resourceName</code> in the Mailcoach API and want to use this :resource, you\'ll need to pass this value', [
                'resourceName' => 'template uuid',
                'resource' => 'template',
            ]) !!}
                <p class="mt-4">
                    <x-mailcoach::code-copy class="flex items-center justify-between max-w-md" :code="$template->uuid"></x-mailcoach::code-copy>
                </p>
            </x-mailcoach::help>
        </div>
    </x-mailcoach::fieldset>
</div>
