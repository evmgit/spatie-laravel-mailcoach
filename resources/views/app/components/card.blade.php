@php($id = \Illuminate\Support\Str::random(4))
@props([
    'buttons' => false,
    'class' => '',
])

<div @if($buttons) id="card-buttons-{{ $id }}" @endif class="{{ $buttons? 'card-buttons' : 'card form-grid' }} {{ $class }}" {{ $attributes->except('class') }}>
    {{ $slot }}
</div>

@if($buttons)
<script>
    const observer{{ $id }} = new IntersectionObserver(
    ([e]) => e.target.classList.toggle('card-buttons-stuck', e.intersectionRatio < 1),
    {threshold: [1]}
    );

    observer{{ $id }}.observe(document.querySelector('#card-buttons-{{ $id }}'))
</script>
@endif
