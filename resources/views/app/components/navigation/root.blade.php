@props([
    'title' => '',
])
<div class="md:sticky md:top-14 {{ $attributes->get('class') }}" {{ $attributes->except('class') }}>
    <div class="p-6 md:px-8 md:py-8 bg-gradient-to-b from-indigo-600/5 to-indigo-600/10 rounded-b-md md:rounded-md bg-clip-padding border border-indigo-700/10">
        @if ($title instanceof \Illuminate\View\ComponentSlot)
            {{ $title }}
        @elseif ($title)
            <h2 class="mb-6 font-extrabold text-sm uppercase tracking-wider truncate">{{ $title }}</h2>
        @endif
        <ul class="flex flex-col gap-2 md:gap-3">
            {{ $slot }}
        </ul>
    </div>
</div>
