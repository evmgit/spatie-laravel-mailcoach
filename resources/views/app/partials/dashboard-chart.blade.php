{{-- The @if statement formatting here is specifically to make empty:hidden work --}}
<div class="empty:hidden card">@if ($stats->count())
    <a href="#" class="mb-4 flex items-center gap-2 hover:text-blue-700">
        <h2 class="markup-h2 mb-0">
            {{ __mc('Audience Growth') }}
        </h2>
    </a>
    <div x-data="dashboardChart" x-init="renderChart({
        labels: @js($stats->pluck('label')->values()->toArray()),
        subscribers: @js($stats->pluck('subscribers')->values()->toArray()),
        subscribes: @js($stats->pluck('subscribes')->values()->toArray()),
        unsubscribes: @js($stats->pluck('unsubscribes')->values()->toArray()),
        campaigns: @js($stats->pluck('campaigns')->values()->toArray()),
    })">
        <canvas id="chart" style="position: relative; max-height:300px; width:100%; max-width: 100%;"></canvas>
        <div class="text-right mt-4">
            <small class="text-gray-500 text-sm">{{ __mc('You can drag the chart to zoom.') }}</small>
            <a x-show="zoomed" x-cloak class="text-gray-500 text-sm underline" href="#" x-on:click.prevent="resetZoom">Reset zoom</a>
        </div>
    </div>
@endif</div>
