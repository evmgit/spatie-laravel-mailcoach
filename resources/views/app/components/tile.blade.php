@props([
    'cols' => 1,
    'rows' => 1,
    'icon' => null,
    'link' => null,
    'danger' => null,
    'warning' => null,
    'success' => null,
    'target' => null,
    'wrapperClass' => '',
])
<!-- md:col-span-1 md:col-span-2 md:col-span-3 md:col-span-4 md:col-span-5 md:col-span-6 md:col-span-7 md:col-span-8 md:col-span-9 md:col-span-10 md:col-span-11 md:col-span-12 -->
<!-- md:row-span-1 md:row-span-2 md:row-span-3 -->

<div class="min-h-[8rem] flex items-center md:row-span-{{ $rows }} md:col-span-{{ $cols }} {{ $attributes->get('class') }} {{ isset($link) ? 'dashboard-tile-hover' : ''}} dashboard-tile {{ $danger ? 'dashboard-tile-error' : ''}} {{ $warning ? 'dashboard-tile-warning' : ''}} {{ $success ? 'dashboard-tile-success' : ''}}" {{ $attributes->except('class') }}>
    @if ($icon)
        <i class="absolute -bottom-3 right-6 {{ $danger ? 'text-red-400/10' : ( $warning ? 'text-orange-400/5' : ( $success ? 'text-green-400/5' : 'text-blue-400/5' )) }} fas fa-{{ $icon }} text-[100px]"></i>
    @endif

    @if ($link)
        <a href='{{$link}}' @if ($target) target="{{ $target }}" @endif class="z-1 absolute inset-0">
        </a>
    @endif

    <div class="pointer-events-none z-10 h-full w-full flex items-center">
        <div class="w-full {{ $wrapperClass }}">
            {{ $slot }}
        </div>
    </div>

</div>
