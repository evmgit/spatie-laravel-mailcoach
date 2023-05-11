@pushonce('endHead')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.css"/>
    <script src="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.js"></script>
    <script>
        Coloris({ alpha: false, format: 'hsl' });
    </script>
    <style>.clr-field button { margin-right: 1px; width: 2.75rem; border-top-right-radius: 2px; border-bottom-right-radius: 2px; }</style>
@endpushonce

<div class="form-field" wire:ignore>
    @if($label ?? null)
        <label class="{{ ($required ?? false) ? 'label label-required' : 'label' }}" for="color-{{ $name }}">
            {{ $label }}

            @if ($help ?? null)
                <i class="ml-1 text-purple-500 opacity-75 cursor-pointer fas fa-question-circle" x-data x-tooltip="{{ $help }}"></i>
            @endif
        </label>
    @endif

    <!-- search is a prefix here because otherwise 1Password shows its widget -->
    <input
        autocomplete="off"
        @if (! $attributes->has('x-bind:type'))
            type="{{ $type ?? 'text' }}"
        @endif
        name="search-{{ $name }}"
        id="search-{{ $name }}"
        class="input {{ $inputClass ?? '' }}"
        placeholder="{{ $placeholder ?? '' }}"
        value="{{ old($name, $value ?? '') }}"
        {{ ($required ?? false) ? 'required' : '' }}
        {!! $attributes ?? '' !!}
        @if($disabled ?? false) disabled @endif
        data-coloris
    >
    @error($name)
    <p class="form-error" role="alert">{{ $message }}</p>
    @enderror
</div>
