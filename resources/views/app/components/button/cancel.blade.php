<button
    {{ $attributes->merge(['type' => 'button', 'class' => 'button-cancel'])->except(['label']) }}
>
    {{ $label ?? __mc('Cancel')  }}
</button>
