@if ((! request()->routeIs('mailConfiguration')) || (! $usesVapor && ! $horizonActive && \Composer\InstalledVersions::isInstalled("laravel/horizon")) || ! $queueConfig)
    @if(\Spatie\Mailcoach\MailcoachServiceProvider::getMailerClass()::count() === 0)
        @include('mailcoach::app.layouts.partials.health-tile-mailers')
    @endif

    @if (! $queueConfig)
        @include('mailcoach::app.layouts.partials.health-tile-queue')
    @endif

    @if(! $usesVapor && ! $horizonActive && \Composer\InstalledVersions::isInstalled("laravel/horizon"))
        @include('mailcoach::app.layouts.partials.health-tile-horizon')
    @endif
@endif
