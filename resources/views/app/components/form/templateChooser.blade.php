@php
    /** @var \Illuminate\Support\Collection $templates */
    $templates = \Spatie\Mailcoach\Mailcoach::getTemplateClass()::all()->pluck('name', 'id');
@endphp

@if(count($templates))
<x-mailcoach::select-field
    class="{{ $attributes->get('class') }}"
    label="Template"
    name="template_id"
    wire:model="templateId"
    :clearable="$attributes->get('clearable', true )"
    :placeholder="__mc('No template')"
    :options="$templates"
/>
@else
<div class="form-field">
    <label class="label">Template</label>
    <div>
        No templates yet, go <a class="link-dimmed" href="{{ route('mailcoach.templates') }}">create one</a>.
    </div>
</div>
@endif
