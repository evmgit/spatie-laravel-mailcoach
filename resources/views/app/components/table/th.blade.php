@props([
    'property' => '',
    'sort' => '',
])
<th {{ $attributes }}>
    @if ($property)
        <a href="#" wire:click.prevent="sort('{{ $property }}')">
            {{ $slot }}

            @if($sort === \Illuminate\Support\Str::replaceFirst('-', '', $property))
                <i class="far fa-arrow-up text-gray-500"></i>
            @elseif($sort === \Illuminate\Support\Str::start($property, '-'))
                <i class="far fa-arrow-down text-gray-500"></i>
            @endif
        </a>
    @else
        {{ $slot }}
    @endif
</th>
