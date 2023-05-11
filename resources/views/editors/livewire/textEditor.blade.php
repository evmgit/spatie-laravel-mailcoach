<div class="form-grid">
    @if ($model->hasTemplates())
        <x-mailcoach::template-chooser />
    @endif

    @foreach($template?->fields() ?? [['name' => 'html', 'type' => 'editor']] as $field)
        <x-mailcoach::editor-fields :name="$field['name']" :type="$field['type']">
            <x-slot name="editor">
                <textarea
                    data-dirty-check
                    class="input input-html"
                    rows="15"
                    wire:model.lazy="templateFieldValues.{{ $field['name'] }}"
                ></textarea>
            </x-slot>
        </x-mailcoach::editor-fields>
    @endforeach

    <x-mailcoach::replacer-help-texts :model="$model" />

    <x-mailcoach::editor-buttons :preview-html="$fullHtml" :model="$model" />
</div>
