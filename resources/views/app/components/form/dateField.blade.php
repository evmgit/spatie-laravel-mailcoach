@props([
    'minDate' => 'today',
    'maxDate' => null,
    'position' => 'above',
    'label' => null,
    'required' => false,
    'name' => null,
    'class' => '',
    'inputClass' => '',
    'value' => '',
    'placeholder' => '',
    'disabled' => false,
])
<div
    x-data="{
        value: @js($value),
        init() {
            let picker;

            let refreshPicker = () => {
                if (picker) {
                    picker.destroy();
                }

                picker = flatpickr(this.$refs.picker, {
                    dateFormat: 'Y-m-d',
                    defaultDate: this.value,
                    @if($minDate) minDate: '{{ $minDate }}', @endif
                    @if($maxDate) maxDate: '{{ $maxDate }}', @endif
                    position: '{{ $position }}',
                })
            }

            refreshPicker()

            this.$watch('value', () => refreshPicker())
        },
    }"
    class="form-field {{ $class }}"
>
    @if($label)
    <label class="{{ $required ? 'label label-required' : 'label' }}" for="{{ $name }}">
        {{ $label }}
    </label>
    @endif
    <input
        x-ref="picker"
        type="text"
        name="{{ $name }}"
        id="{{ $name }}"
        class="input max-w-xs {{ $inputClass }}"
        style="opacity: 1;"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {!! $attributes ?? '' !!}
        @if($disabled) disabled @endif
    >
    @error($name)
        <p class="form-error" role="alert">{{ $message }}</p>
    @enderror
</div>
