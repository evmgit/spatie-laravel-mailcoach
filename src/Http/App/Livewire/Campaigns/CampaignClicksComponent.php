<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Queries\CampaignLinksQuery;
use Spatie\Mailcoach\MainNavigation;

class CampaignClicksComponent extends DataTableComponent
{
    public string $sort = '-unique_click_count';

    public Campaign $campaign;

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;

        app(MainNavigation::class)->activeSection()?->add($this->campaign->name, route('mailcoach.campaigns'));
    }

    public function getTitle(): string
    {
        return __mc('Clicks');
    }

    public function getView(): string
    {
        return 'mailcoach::app.campaigns.clicks';
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
        return [
            'campaign' => $this->campaign,
            'links' => (new CampaignLinksQuery($this->campaign, $request))->paginate($request->per_page),
            'totalLinksCount' => $this->campaign->links()->count(),
        ];
    }
}
