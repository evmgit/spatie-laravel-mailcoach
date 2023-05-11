<div wire:init="loadData">
    <h1 class="text-xl text-gray-600 -mt-6 mb-4">
        Hi, <strong>{{ str(Auth::guard(config('mailcoach.guard'))->user()->name)->ucfirst() }}</strong>
    </h1>
    <div class="grid md:grid-cols-12 gap-6">
        @if ((new Spatie\Mailcoach\Domain\Shared\Support\License\License())->hasExpired())
            <x-mailcoach::tile class="bg-orange-100" cols="3" icon="credit-card">
                <x-slot:link><a class="underline" href="https://spatie.be/products/mailcoach">Renew license</a></x-slot:link>
                Your Mailcoach license has expired. <a class="underline" href="https://spatie.be/products/mailcoach">Renew your license</a> and benefit from fixes and new features.
            </x-mailcoach::tile>
        @endif

        @include('mailcoach::app.layouts.partials.beforeDashboardTiles')

        <x-mailcoach::tile cols="3" icon="users" link="{{ route('mailcoach.emailLists') }}">
            <h2 class="dashboard-title">
                {{ __mc('New subscribers') }}
            </h2>
            <div class="flex flex-col">
                <span class="dashboard-value">{{ !is_null($recentSubscribers) ? $this->abbreviateNumber($recentSubscribers) : '...' }}</span>
                <span class="dashboard-label">{{ __mc('Last 30 days') }}</span>
            </div>
        </x-mailcoach::tile>

        <x-mailcoach::tile class="" cols="3" icon="envelope-open-text" link="{{ route('mailcoach.campaigns') }}">
            <h2 class="dashboard-title">
                @if ($totalCount = $this->getCampaignClass()::count())
                    {{ $this->abbreviateNumber($totalCount) }} {{ __mc('Campaigns') }}
                @else
                    {{ __mc('Create your first campaign') }}
                @endif
            </h2>
            <div class="flex justify-between">
                @if ($draftCount = $this->getCampaignClass()::draft()->count())
                    <a href="{{ route('mailcoach.campaigns') }}?status=draft" class="dashboard-link">
                        <span class="dashboard-value">{{ $this->abbreviateNumber($draftCount) }}</span>
                        <span class="dashboard-label">{{ __mc('Draft') }}</span>
                    </a>
                @endif

                @if ($scheduledCount = $this->getCampaignClass()::scheduled()->count())
                    <a href="{{ route('mailcoach.campaigns') }}?status=scheduled" class="dashboard-link">
                        <span class="dashboard-value">{{ $this->abbreviateNumber($scheduledCount) }}</span>
                        <span class="dashboard-label">Scheduled</span>
                    </a>
                @endif

                @if ($sentCount = $this->getCampaignClass()::sent()->count())
                    <a href="{{ route('mailcoach.campaigns') }}?status=sent" class="dashboard-link">
                        <span class="dashboard-value">{{ $this->abbreviateNumber($sentCount) }}</span>
                        <span class="dashboard-label">Sent</span>
                    </a>
                @endif
            </div>
        </x-mailcoach::tile>

        @if ($latestCampaign)
            <x-mailcoach::tile class="" cols="4" icon="paper-plane" link="{{ route('mailcoach.campaigns.summary', $latestCampaign) }}">
                <h2 class="dashboard-title">{{ $latestCampaign->name }}</h2>

                <div class="flex justify-between">
                    <a href="{{ route('mailcoach.campaigns.opens', $latestCampaign) }}" class="dashboard-link">
                        <span class="dashboard-value">{{ $this->abbreviateNumber($latestCampaign->unique_open_count) }}</span>
                        <span class="dashboard-label">Opens</span>
                    </a>

                    <a href="{{ route('mailcoach.campaigns.clicks', $latestCampaign) }}" class="dashboard-link">
                        <span class="dashboard-value">{{ $this->abbreviateNumber($latestCampaign->unique_click_count) }}</span>
                        <span class="dashboard-label">Clicks</span>
                    </a>

                    <a href="{{ route('mailcoach.campaigns.unsubscribes', $latestCampaign) }}" class="dashboard-link">
                        <span class="dashboard-value">{{ $this->abbreviateNumber($latestCampaign->unsubscribe_count) }}</span>
                        <span class="dashboard-label">Unsubscribes</span>
                    </a>

                    <a href="{{ route('mailcoach.campaigns.outbox', $latestCampaign) }}" class="dashboard-link">
                        <span class="dashboard-value">{{ $this->abbreviateNumber($latestCampaign->bounce_count) }}</span>
                        <span class="dashboard-label">Bounces</span>
                    </a>
                </div>
            </x-mailcoach::tile>
        @endif

        @include('mailcoach::app.layouts.partials.beforeDashboardGraph')

        <div class="md:col-span-12">
            <livewire:mailcoach::dashboard-chart />
        </div>

        @include('mailcoach::app.layouts.partials.afterDashboardGraph')
    </div>
</div>
