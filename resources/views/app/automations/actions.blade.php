<form
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
    method="POST"
>
<div class="card-grid pb-48">
    <livewire:mailcoach::automation-builder name="default" :automation="$automation" :actions="$actions" />

    <x-mailcoach::card buttons>
            <div class="flex items-center gap-6">
            @if ($unsavedChanges)
                <x-mailcoach::button :label="__mc('Save actions')" :disabled="count($editingActions) > 0" />
            @endif

            @if (count($editingActions) > 0)
                <span class="inline-flex gap-1.5 items-center text-gray-500">
                    <x-mailcoach::rounded-icon type="info" />
                    <span class="text-sm">@lang('Save your individual actions first')</span>
                </span>
            @endif
            </div>
        </x-mailcoach::card>
</div>
</form>
