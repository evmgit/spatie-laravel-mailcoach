@if (! request()->routeIs('mailcoach.dashboard'))
    @if ((! request()->routeIs('mailConfiguration') && \Spatie\Mailcoach\MailcoachServiceProvider::getMailerClass()::count() === 0) || (! $usesVapor && ! $horizonActive && \Composer\InstalledVersions::isInstalled("laravel/horizon")) || ! $queueConfig)
        <div class="max-w-layout mx-auto flex flex-col gap-1">
            @if (! request()->routeIs('mailConfiguration'))
                @if(\Spatie\Mailcoach\MailcoachServiceProvider::getMailerClass()::count() === 0)
                    @include('mailcoach::app.layouts.partials.health-tile-mailers')
                @endif
            @endif

            @if (! $queueConfig)
                @include('mailcoach::app.layouts.partials.health-tile-queue')
            @endif

            @if(! $usesVapor && ! $horizonActive && \Composer\InstalledVersions::isInstalled("laravel/horizon"))
                @include('mailcoach::app.layouts.partials.health-tile-horizon')
            @endif
        </div>
    @endif
@endif
