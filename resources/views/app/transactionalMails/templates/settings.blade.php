<form
    class="card-grid"
    method="POST"
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
    x-data="{ type: @entangle('template.type') }"
    x-cloak
>
    <x-mailcoach::fieldset card :legend="__mc('General')">
        <x-mailcoach::text-field :label="__mc('Name')" name="template.name" wire:model.lazy="template.name" required />
        <x-mailcoach::info>
            {{ __mc('This name is used by the application to retrieve this template. Do not change it without updating the code of your app.') }}
        </x-mailcoach::info>

        <?php
        $editor = config('mailcoach.content_editor', \Spatie\Mailcoach\Http\App\Livewire\TextAreaEditorComponent::class);
        $editorName = (new ReflectionClass($editor))->getShortName();
        ?>
        <x-mailcoach::select-field
            :label="__mc('Format')"
            name="template.type"
            wire:model="template.type"
            :options="[
                'html' => 'HTML (' . $editorName . ')',
                'markdown' => 'Markdown',
                'blade' => 'Blade',
                'blade-markdown' => 'Blade with Markdown',
            ]"
        />

        <div x-show="type === 'blade'">
            <x-mailcoach::warning>
                <p class="text-sm mb-2">{{ __mc('Blade templates have the ability to run arbitrary PHP code. Only select Blade if you trust all users that have access to the Mailcoach UI.') }}</p>
            </x-mailcoach::warning>
        </div>

        <div x-show="type === 'blade-markdown'">
            <x-mailcoach::warning>
                <p class="text-sm mb-2">{{ __mc('Blade templates have the ability to run arbitrary PHP code. Only select Blade if you trust all users that have access to the Mailcoach UI.') }}</p>
            </x-mailcoach::warning>
        </div>

        <x-mailcoach::checkbox-field :label="__mc('Store mail')" name="template.store_mail" wire:model.lazy="template.store_mail" />
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__mc('Tracking')">
        <div class="form-field">
            <x-mailcoach::info>
                {!! __mc('Open & Click tracking are managed by your email provider.') !!}
            </x-mailcoach::info>
        </div>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__mc('Usage in Mailcoach API')">
        <div>
            <x-mailcoach::help>
                {!! __mc('Whenever you need to specify a <code>:resourceName</code> in the Mailcoach API and want to use this :resource, you\'ll need to pass this value', [
                'resourceName' => 'transactionalMailTemplate uuid',
                'resource' => 'transactional email',
            ]) !!}
                <p class="mt-4">
                    <x-mailcoach::code-copy class="flex items-center justify-between max-w-md" :code="$template->uuid"></x-mailcoach::code-copy>
                </p>
            </x-mailcoach::help>
        </div>
    </x-mailcoach::fieldset>

    <x-mailcoach::card buttons>
        <x-mailcoach::button :label="__mc('Save settings')" />
</x-mailcoach::card>

</form>
