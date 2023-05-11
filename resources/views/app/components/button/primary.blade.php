<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'button'])->except(['label']) }}
>
    {{ $label ?? $slot ?? __mc('Save')  }}
</button>
