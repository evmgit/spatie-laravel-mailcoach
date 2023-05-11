<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="referrer" content="always">

        <link rel="preconnect" href="https://fonts.gstatic.com">

        <title>{{ isset($title) ? "{$title} |" : '' }} {{ isset($originTitle) ? "{$originTitle} |" : '' }} Mailcoach</title>

        {!! \Spatie\Mailcoach\Mailcoach::styles() !!}

        {!! \Livewire\Livewire::styles() !!}
        <script type="text/javascript">
            window.__ = function (key) {
                return {
                    "Are you sure?": "{{ __mc('Are you sure?') }}",
                    "Type to add tags": "{{ __mc('Type to add tags') }}",
                    "No tags to choose from": "{{ __mc('No tags to choose from') }}",
                    "Press to add": "{{ __mc('Press to add') }}",
                    "Press to select": "{{ __mc('Press to select') }}",
                }[key];
            };
        </script>

        @include('mailcoach::app.layouts.partials.endHead')
        @stack('endHead')
    </head>
    <body class="flex flex-col min-h-screen text-gray-800 bg-indigo-900/5" x-data="{ confirmText: '', onConfirm: null }">
        <script>/**/</script><!-- Empty script to prevent FOUC in Firefox -->

        <div class="flex-grow">
            <header class="flex-none sticky top-0 z-20 w-full max-w-layout mx-auto px-0 md:px-16">
                <x-mailcoach::main-navigation />
            </header>

            <main class="md:pt-10 px-6 md:px-16 relative flex-grow z-1 mx-auto w-full max-w-layout md:flex md:items-stretch md:gap-10">
                @isset($nav)
                    <nav class="-mt-2 mb-4 md:my-0 flex-none md:w-[16rem]">
                        {{ $nav }}
                    </nav>
                @endisset

                <section class="flex-grow min-w-0 flex flex-col">
                    @unless(isset($hideBreadcrumbs) && $hideBreadcrumbs)
                        <nav class="mt-6 md:mt-0 flex-none">
                            @include('mailcoach::app.layouts.partials.breadcrumbs')
                        </nav>
                    @endunless

                    <div class="flex-none flex items-center">
                        <h1 class="mt-1 markup-h1">
                            {{ $title ?? '' }}
                        </h1>
                        {{ $header ?? '' }}
                    </div>

                    <div>
                       {{ $slot }}
                    </div>
                </section>
            </main>

            <x-mailcoach::modal :title="__mc('Confirm')" name="confirm" :dismissable="true">
                <span x-text="confirmText"></span>

                <x-mailcoach::form-buttons>
                    <x-mailcoach::button data-confirm type="button" x-on:click="onConfirm; $store.modals.close('confirm')" :label=" __mc('Confirm')" />
                    <x-mailcoach::button-cancel  x-on:click="$store.modals.close('confirm')" :label=" __mc('Cancel')" />
                </x-mailcoach::form-buttons>
            </x-mailcoach::modal>

            <x-mailcoach::modal :title="__mc('Confirm navigation')" name="dirty-warning">
                {{ __mc('There are unsaved changes. Are you sure you want to continue?') }}

                <x-mailcoach::form-buttons>
                    <x-mailcoach::button type="button" x-on:click="$store.modals.onConfirm && $store.modals.onConfirm()" :label=" __mc('Confirm')" />
                    <x-mailcoach::button-cancel  x-on:click="$store.modals.close('dirty-warning')" :label=" __mc('Cancel')" />
                </x-mailcoach::form-buttons>
            </x-mailcoach::modal>

            @stack('modals')
        </div>

        <footer class="mt-10">
            @include('mailcoach::app.layouts.partials.footer')
        </footer>

        <aside class="z-50 fixed bottom-4 left-4 w-64">
            @include('mailcoach::app.layouts.partials.startBody')

            @if ((new Spatie\Mailcoach\Domain\Shared\Support\License\License())->hasExpired())
                <div class="alert alert-warning text-sm shadow-lg">
                    Your Mailcoach license has expired. <a class="underline" href="https://spatie.be/products/mailcoach">Renew your license</a> and benefit from fixes and new features.
                </div>
            @endif

            @include('mailcoach::app.layouts.partials.flash')
        </aside>

        {!! \Livewire\Livewire::scripts() !!}
        @livewire('livewire-ui-spotlight')
        {!! \Spatie\Mailcoach\Mailcoach::scripts() !!}
    </body>
</html>
