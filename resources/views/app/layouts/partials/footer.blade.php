<div class="mx-auto w-full max-w-layout px-6 py-16 md:px-16 flex flex-col gap-y-4 lg:flex-row lg:gap-x-16">
    <div class="text-sm text-gray-500 flex items-center" x-cloak x-data="{ key: 'CMD' }" x-init="platform = window.navigator.platform.indexOf('Mac') ? 'CMD' : 'CTRL' ">
        <x-mailcoach::icon-label icon="far fa-lightbulb" />
        <span>
            <strong>ProTip!</strong> You can use <kbd><span x-text="key"></span>+/</kbd> to open the command palette.
        </span>
    </div>
    <div class="lg:ml-auto flex flex-wrap items-center text-sm text-gray-500">
        <a class="inline-block truncate max-w-[6rem]" href="https://mailcoach.app">
            Mailcoach {{ $versionInfo->getCurrentVersion() }}
        </a>
        <span>&nbsp;{{ __mc('by') }} <a class="" target="_blank" href="https://spatie.be">SPATIE</a></span>

        @if(Auth::guard(config('mailcoach.guard'))->check())
            <span class="mx-2">•</span>
            <a class="" href="https://mailcoach.app/docs" target="_blank">{{ __mc('Documentation') }}</a>

            <span class="mx-2">•</span>
            <a class="inline-block" href="{{ route('debug') }}">
                Debug
            </a>
            <span class="mx-2">•</span>
            <a class="inline-block" href="{{ route('export') }}">
                Export
            </a>
            <span class="mx-1">/</span>
            <a class="inline-block" href="{{ route('import') }}">
                Import
            </a>

            @if(! $versionInfo->isLatest())
                <a class="ml-4 inline-flex items-center" href="/">
                    <i class="fas fa-horse-head mr-1"></i>
                    {{ __mc('Upgrade available') }}
                </a>
            @endif
        @endif

        @if (! app()->environment('production') || config('app.debug'))
            <span class="ml-4 inline-flex items-center">
                <i class="text-red-500 far fa-wrench mr-1"></i>
                Env: {{ app()->environment() }} &mdash; Debug: {{ config('app.debug') ? 'true' : 'false' }}
            </span>
        @endif
    </div>
</div>
