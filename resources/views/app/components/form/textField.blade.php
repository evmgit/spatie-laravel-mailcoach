<div class="form-field {{ $wrapperClass ?? '' }}">
    @if($label ?? null)
    <label class="{{ ($required ?? false) ? 'label label-required' : 'label' }}" for="{{ $name }}">
        {{ $label }}

        @if ($help ?? null)
            <i class="ml-1 text-purple-500 opacity-75 cursor-pointer fas fa-question-circle" x-data x-tooltip="{{ $help }}"></i>
        @endif
    </label>
    @endif
    <input
        autocomplete="off"
        @if (! $attributes->has('x-bind:type'))
        type="{{ $type ?? 'text' }}"
        @endif
        name="{{ $name }}"
        id="{{ $name }}"
        class="input {{ $inputClass ?? '' }}"
        placeholder="{{ $placeholder ?? '' }}"
        value="{{ old($name, $value ?? '') }}"
        {{ ($required ?? false) ? 'required' : '' }}
        {!! $attributes ?? '' !!}
        @if($disabled ?? false) disabled @endif
    >
    @error($name)
        <p class="form-error" role="alert">{{ $message }}</p>
    @enderror
</div>
