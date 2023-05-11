<div class="form-field">
    @if($label ?? null)
    <label class="{{ ($required ?? false) ? 'label label-required' : 'label' }}" for="{{ $name }}">
        {{ $label }}

        @if ($help ?? null)
            <i class="ml-1 text-purple-500 opacity-75 cursor-pointer fas fa-question-circle" x-data x-tooltip="{{ $help }}"></i>
        @endif
    </label>
    @endif
    <textarea
        @if($disabled ?? false) disabled @endif
        name="{{ $name }}"
        id="{{ $name }}"
        lines="15"
        class="input {{ $inputClass ?? '' }}"
        placeholder="{{ $placeholder ?? '' }}"
        {{ $attributes }}
    >
        {{ old($name, $value ?? '') }}
    </textarea>
    @error($name)
        <p class="form-error" role="alert">{{ $message }}</p>
    @enderror
</div>
