@props([
    'previewHtml' => '',
    'model' => null,
])
@if($model instanceof \Spatie\Mailcoach\Domain\Shared\Models\Sendable)
@pushonce('endHead')
<script>
    document.addEventListener('livewire:load', function () {
        setInterval(() => @this.autosave(), 20000);
    });
</script>
@endif
@endpushonce
<x-mailcoach::form-buttons>
    <x-mailcoach::button
        @keydown.prevent.window.cmd.s="$wire.call('save')"
        @keydown.prevent.window.ctrl.s="$wire.call('save')"
        wire:click.prevent="save"
        :label="__mc('Save content')"
    />

    @if (method_exists($model, 'sendTestMail') && (\Spatie\Mailcoach\Mailcoach::defaultCampaignMailer() || \Spatie\Mailcoach\Mailcoach::defaultAutomationMailer() || \Spatie\Mailcoach\Mailcoach::defaultTransactionalMailer()))
        <x-mailcoach::button x-on:click.prevent="$wire.saveQuietly() && $store.modals.open('send-test')" :label="__mc('Save and send test')"/>
        <x-mailcoach::modal name="send-test" :dismissable="true">
            <livewire:mailcoach::send-test :model="$model" />
        </x-mailcoach::modal>
    @endif

    @if (config('mailcoach.content_editor') !== \Spatie\MailcoachUnlayer\UnlayerEditor::class)
    <x-mailcoach::button-secondary x-on:click.prevent="$store.modals.open('preview')" :label="__mc('Preview')"/>
    <x-mailcoach::preview-modal name="preview" :html="$previewHtml" :title="__mc('Preview') . ($model->subject ? ' - ' . $model->subject : '')" />
    @endif

    {{ $slot }}

    @if ($model instanceof \Spatie\Mailcoach\Domain\Shared\Models\Sendable)
        @if ($this->autosaveConflict)
            <x-mailcoach::warning class="mt-4">
                {{ __mc('Autosave disabled, the content was saved somewhere else. Refresh the page to get the latest content or save manually to override.') }}
            </x-mailcoach::warning>
        @else
            <p class="text-xs mt-3">{{ __mc("We autosave every 20 seconds") }} - {{ __mc('Last saved at') }} {{ $model->updated_at->toMailcoachFormat() }}</p>
        @endif
    @endif
</x-mailcoach::form-buttons>
