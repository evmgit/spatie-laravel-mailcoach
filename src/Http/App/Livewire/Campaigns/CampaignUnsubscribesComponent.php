<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Queries\CampaignUnsubscribesQuery;
use Spatie\Mailcoach\MainNavigation;

class CampaignUnsubscribesComponent extends DataTableComponent
{
    public string $sort = '-created_at';

    public Campaign $campaign;

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;

        app(MainNavigation::class)->activeSection()?->add($this->campaign->name, route('mailcoach.campaigns'));
    }

    public function getTitle(): string
    {
        return __mc('Unsubscribes');
    }

    public function getView(): string
    {
        return 'mailcoach::app.campaigns.unsubscribes';
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.campaigns.layouts.campaign';
    }

    public function getLayoutData(): array
    {
        return [
            'campaign' => $this->campaign,
        ];
    }

    public function getData(Request $request): array
    {
        $this->authorize('view', $this->campaign);

        return [
            'campaign' => $this->campaign,
            'unsubscribes' => (new CampaignUnsubscribesQuery($this->campaign, $request))->paginate($request->per_page),
            'totalUnsubscribes' => $this->campaign->unsubscribes()->count(),
        ];
    }
}
