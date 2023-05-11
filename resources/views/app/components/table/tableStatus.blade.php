<div class="flex flex-wrap items-center gap-x-6 gap-y-2">
    @if($paginator->total() !== $totalCount)
    <p class="table-status whitespace-nowrap">
            {{ __mc('Filtering :resource', [
                'resource' => \Illuminate\Support\Str::plural($name),
            ]) }}.
            <a href="#" {{ $attributes->wire('click') }} class="link-dimmed">
                {{ __mc('Show all') }}
            </a>
        </p>
    @endif
    <div class="flex-grow">
        {{ $paginator->links('mailcoach::app.components.table.pagination') }}
    </div>
</div>
