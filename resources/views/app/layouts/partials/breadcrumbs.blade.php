@php($breadcrumbs = app($breadcrumbsNavigationClass ?? Spatie\Mailcoach\MainNavigation::class)->breadcrumbs())
@php($previousBreadcrumb = null)
<div class="pl-1 flex items-center gap-x-2 text-xs text-gray-500">
    @foreach ($breadcrumbs as $index => $breadcrumb)
        @continue($breadcrumb === $previousBreadcrumb)
        @if (! $loop->first)
            <i class="fa fa-angle-right text-gray-400"></i>
        @endif
        <a class="hover:text-blue-800 last:font-semibold min-w-0 truncate" href="{{ $breadcrumb['url'] }}" data-dirty-warn>{{ $breadcrumb['title'] }}</a>
        @php($previousBreadcrumb = $breadcrumb)
    @endforeach

    @if (isset($title) && (string) $title !== ($breadcrumb['title'] ?? ''))
        <i class="fa fa-angle-right text-gray-400"></i>
        <span class="font-semibold min-w-0 truncate">{{ $title }}</span>
    @endif
</div>
