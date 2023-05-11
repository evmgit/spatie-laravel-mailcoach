@props([
    'card' => false,
    'clean' => false,
    'focus' => false,
    'class' => '',
    'legend' => null
])

<fieldset {{ $attributes->except('class') }} class="{{ $card? 'card form-grid' : ($clean? 'form-fieldset-clean' : 'form-fieldset') }} {{ $class }} {{ $focus ? 'card-focus' : '' }}">
    @isset($legend)
        <div class="form-legend">
            {{ $legend }}
        </div>
    @endisset
    {{ $slot }}
</fieldset>
