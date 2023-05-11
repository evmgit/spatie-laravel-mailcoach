@php($id = \Illuminate\Support\Str::random(4))
<div id="form-buttons-{{ $id }}" class="form-buttons {{ $attributes->get('class') }}" {{ $attributes->except('class') }}>
    {{ $slot }}
</div>

<script>
  const observer{{ $id }} = new IntersectionObserver(
    ([e]) => e.target.classList.toggle('form-buttons-stuck', e.intersectionRatio < 1),
    {threshold: [1]}
  );
  observer{{ $id }}.observe(document.querySelector('#form-buttons-{{ $id }}'))
</script>
