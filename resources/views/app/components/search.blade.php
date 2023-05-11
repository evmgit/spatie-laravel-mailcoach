@props([
    'placeholder' => '',
    'value' => '',
])
<div class="search {{ $class ?? '' }}">
    <input type="search" {{ $attributes->except('class') }} required placeholder="{{ $placeholder }}" value="{{ $value }}"
        autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
    <div class="search-icon">
        <i class="fas fa-search"></i>
    </div>
</div>
