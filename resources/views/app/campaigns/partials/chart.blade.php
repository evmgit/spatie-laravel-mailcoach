<div>
    @if ($stats->count())
        <div x-data="campaignStatisticsChart" x-init="renderChart({
            labels: @js($stats->pluck('label')->values()->toArray()),
            opens: @js($stats->pluck('opens')->values()->toArray()),
            clicks: @js($stats->pluck('clicks')->values()->toArray()),
        })">
            <canvas id="chart" style="position: relative; max-height:300px; width:100%; max-width: 100%;"></canvas>
            <div class="relative text-right -mb-8" style="top: -2rem">
                <small class="text-gray-500 text-sm">{{ __mc('You can drag the chart to zoom.') }}</small>
                <a x-show="zoomed" x-cloak class="text-gray-500 text-sm underline" href="#" x-on:click.prevent="resetZoom">{{ __mc('Reset zoom') }}</a>
            </div>
        </div>
    @endif
</div>
