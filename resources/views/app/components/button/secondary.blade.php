<button
    {{ $attributes->merge(['type' => 'button', 'class' => 'button-secondary'])->except(['label']) }}
>
    {{ $label ?? __mc('Save')  }}
</button>
