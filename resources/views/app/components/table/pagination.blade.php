@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __mc('Pagination Navigation') }}" class="flex items-center justify-between">
        <div class="flex justify-end flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-300 cursor-default leading-5 rounded-md">
                    {!! __mc('Previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" wire:click.prevent="previousPage" wire:loading.attr="disabled" class="relative inline-flex items-center px-4 py-2 text-sm font-medium link leading-5 rounded-md focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:transition ease-in-out duration-150">
                    {!! __mc('Previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" wire:click.prevent="nextPage" wire:loading.attr="disabled" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium link leading-5 rounded-md focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:transition ease-in-out duration-150">
                    {!! __mc('Next') !!}
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-300 cursor-default leading-5 rounded-md">
                    {!! __mc('Next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between sm:flex-wrap gap-x-6 gap-y-2">
            <div>
                <p class="text-sm whitespace-nowrap">
                    <span>{!! __mc('Showing') !!}</span>
                    <span class="font-medium">{{ $paginator->firstItem() }}</span>
                    <span>{!! __mc('to') !!}</span>
                    <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    <span>{!! __mc('of') !!}</span>
                    <span class="font-medium">{{ number_format($paginator->total()) }}</span>
                    <span>{!! __mc('results') !!}</span>
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex items-center gap-2">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __mc('pagination.previous') }}">
                            <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-300 cursor-default rounded-l-md leading-5" aria-hidden="true">
                               <i class="fas fa-arrow-left"></i>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" wire:click.prevent="previousPage" wire:loading.attr="disabled" rel="prev" class="relative inline-flex items-center px-2 py-2 text-sm font-medium rounded-l-md leading-5 hover:text-blue-700 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-300 transition ease-in-out duration-150" aria-label="{{ __mc('pagination.previous') }}">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative  inline-flex items-center justify-center px-2 w-8 h-8 text-sm font-medium cursor-default leading-5">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center justify-center px-2 w-8 h-8 text-sm font-bold bg-indigo-900/10 rounded-full cursor-default leading-5">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" wire:click.prevent="gotoPage({{ $page }})" wire:loading.attr="disabled" class="relative inline-flex items-center justify-center px-2 w-8 h-8 text-sm font-medium hover:text-blue-700 leading-5 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:transition ease-in-out duration-150" aria-label="{{ __mc('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" wire:click.prevent="nextPage" wire:loading.attr="disabled" rel="next" class="relative inline-flex items-center px-2 py-2 text-sm font-medium rounded-r-md leading-5 hover:text-blue-700 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-300 transition ease-in-out duration-150" aria-label="{{ __mc('pagination.next') }}">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __mc('pagination.next') }}">
                            <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-300 cursor-default rounded-r-md leading-5" aria-hidden="true">
                                <i class="fas fa-arrow-right"></i>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
