<form
    class="card-grid"
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
    method="POST"
    x-cloak
>
    @csrf
    <x-mailcoach::fieldset card :legend="__mc('1. Pick an editor to compose campaigns & emails')">
        <x-mailcoach::select-field
            name="contentEditor"
            wire:model="contentEditor"
            :options="$contentEditorOptions"
        />

        @foreach(config('mailcoach.editors') as $editor)
            @if($contentEditor === $editor::label())
                <div class="form-grid">
                    @includeIf($editor::settingsPartial())
                </div>
            @endif
        @endforeach
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__mc('2. Pick an editor to set up templates')">
        <x-mailcoach::select-field
            name="templateEditor"
            wire:model="templateEditor"
            :options="$templateEditorOptions"
        />
        @foreach(config('mailcoach.editors') as $editor)
            @if($templateEditor === $editor::label())
                <div wire:key="{{ $editor }}">
                    @if($templateEditor === $contentEditor)
                        <x-mailcoach::info>
                            {{ __mc('Uses same settings as the content editor.') }}
                        </x-mailcoach::info>
                    @else
                    <div class="form-grid">
                        @includeIf($editor::settingsPartial())
                    </div>
                    @endif
                </div>
            @endif
        @endforeach
    </x-mailcoach::fieldset>

    <x-mailcoach::card buttons>
        <x-mailcoach::button :label="__mc('Save')" />
    </x-mailcoach::card>
</form>
