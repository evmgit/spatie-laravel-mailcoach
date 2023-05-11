<label class="checkbox-label" for="{{ $name }}">
    <input
    type="checkbox"
    name="{{ $name }}"
    id="{{ $name }}"
    value="{{ $value ?? 1 }}"
    @if(old($name, $checked ?? false)) checked @endif
    @if($disabled ?? false) disabled @endif
    {{ $attributes->class('checkbox') }}
    >
    <span>{{ $label }}</span>
</label>
@error($name)
    <p class="form-error" role="alert">{{ $message }}</p>
@enderror
