<div>
    <form
        class="card-grid"
        method="POST"
        wire:submit.prevent="save"
        @keydown.prevent.window.cmd.s="$wire.call('save')"
        @keydown.prevent.window.ctrl.s="$wire.call('save')"
    >
        <x-mailcoach::fieldset card :legend="__mc('Recipients')">
            <x-mailcoach::info>
                {{ __mc('These recipients will be merged with the ones when the mail is sent. You can specify multiple recipients comma separated.') }}
            </x-mailcoach::info>
            <x-mailcoach::text-field placeholder="john@example.com, jane@example.com" :label="__mc('To')"
                                     name="template.to" wire:model.lazy="template.to"/>
            <x-mailcoach::text-field placeholder="john@example.com, jane@example.com" :label="__mc('Cc')"
                                     name="template.cc" wire:model.lazy="template.cc"/>
            <x-mailcoach::text-field placeholder="john@example.com, jane@example.com" :label="__mc('Bcc')"
                                     name="template.bcc" wire:model.lazy="template.bcc"/>
        </x-mailcoach::fieldset>

        <x-mailcoach::fieldset card :legend="__mc('Email')">

            <x-mailcoach::text-field
                :label="__mc('Subject')"
                name="template.subject"
                wire:model.lazy="template.subject"
                required
            />

            @if ($template->type === 'html')
                <div class="mt-6">
                    @livewire(\Livewire\Livewire::getAlias(config('mailcoach.content_editor')), ['model' => $template])
                </div>
            @else
                <?php
                $editor = config('mailcoach.content_editor', \Spatie\Mailcoach\Http\App\Livewire\TextAreaEditorComponent::class);
                $editorName = (new ReflectionClass($editor))->getShortName();
                ?>
                <x-mailcoach::html-field label="{{ [
                    'html' => 'HTML (' . $editorName . ')',
                    'markdown' => 'Markdown',
                    'blade' => 'Blade',
                    'blade-markdown' => 'Blade with Markdown',
                ][$template->type] }}" name="template.body" wire:model.lazy="template.body" />

                <x-mailcoach::editor-buttons :model="$template" :preview-html="$template->body" />
            @endif
        </x-mailcoach::fieldset>
    </form>

    @if($template->canBeTested())
        <x-mailcoach::modal :title="__mc('Send Test')" name="send-test" :dismissable="true">
            @include('mailcoach::app.transactionalMails.templates.partials.test')
        </x-mailcoach::modal>
    @endif

    <x-mailcoach::replacer-help-texts :model="$template" />
</div>
