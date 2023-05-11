<div class="form-field max-w-full">
    @if($label ?? null)
        <label class="{{ ($required ?? false) ? 'label label-required' : 'label' }}" for="{{ $name }}">
            {{ $label }}

            @if ($help ?? null)
                <i class="ml-1 text-purple-500 opacity-75 cursor-pointer fas fa-question-circle" x-data x-tooltip="{{ $help }}"></i>
            @endif
        </label>
    @endif
    <textarea
        class="input input-html"
        {{ ($required ?? false) ? 'required' : '' }}
        rows="20"
        id="{{ $name }}"
        name="{{ $name }}"

        @if(! ($noPreviewSource ?? false))
        data-html-preview-source
        @endunless
        @if($disabled ?? false) disabled @endif
        {{ $attributes }}
    >{{ old($name, $value ?? '') }}</textarea>
    @error($name)
    <p class="form-error" role="alert">{{ $message }}</p>
    @enderror
</div>
