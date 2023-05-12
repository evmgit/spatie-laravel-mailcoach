<div class="form-field">
    @if($label ?? null)
    <label class="{{ ($required ?? false) ? 'label label-required' : 'label' }}" for="{{ $name }}">
        {{ $label }}
    </label>
    @endif
    <input
        type="text"
        name="{{ $name }}"
        id="{{ $name }}"
        class="input max-w-xs {{ $inputClass ?? '' }}"
        value="{{ old($name, $value ?? '') }}"
        data-datepicker="true"
        placeholder="{{ $placeholder ?? '' }}"
        {{ ($required ?? false) ? 'required' : '' }}
        {!! $attributes ?? '' !!}
        @if($disabled ?? false) disabled @endif
    >
    @error($name)
        <p class="form-error" role="alert">{{ $message }}</p>
    @enderror
</div>
