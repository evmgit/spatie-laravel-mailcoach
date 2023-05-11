@php
    use Illuminate\Support\Str;
    $invers = $invers ?? false;
@endphp

<span class="icon-label {{ $invers ? 'flex-row-reverse' : ''}} {{ $attributes->get('class') }}">
    @isset($count)
        <span class="flex">
            <span class="counter ml-0">{{ $count }} </span>
        </span>
    @else
        <span class="w-5 flex justify-center">
            <i class="{{ $icon ?? 'fas fa-arrow-right' }} {{ $caution ?? null ? 'icon-label-icon-caution' : 'icon-label-icon' }}"></i>
        </span>
    @endisset

    @if ($text ?? '' || (isset($count) && isset($countText)))
        <span class="icon-label-text">
            {{ $text ?? '' }}
            {{ isset($count) && isset($countText) ? Str::plural($countText, $count) : ''}}
        </span>
    @endif
</span>
