@props([
    'html' => '',
    'name' => 'preview',
    'title' => 'Preview',
])
<div class="hidden">
    <input type="hidden" id="preview-content" value="{{ base64_encode($html) }}">

    <x-mailcoach::modal
        x-effect="
            const open = $store.modals.isOpen('{{ $name }}');
            if (! document.getElementById('{{ $name }}-iframe')) return;
            document.getElementById('{{ $name }}-iframe').src = 'data:text/html;base64,' + document.getElementById('preview-content').value;
        "
        :name="$name"
        large
        :open="request()->get('modal') === $name"
        :dismissable="true"
    >
        <x-slot:title>
            <p class="mb-2">{{ $title }}</p>
            <x-mailcoach::info class="text-base font-normal" full>{{ __mc('Placeholders won\'t be filled in previews') }}</x-mailcoach::info>
        </x-slot:title>
        <iframe style="width: 100%; height: 100%;" id="{{ $name }}-iframe"></iframe>
    </x-mailcoach::modal>
</div>
