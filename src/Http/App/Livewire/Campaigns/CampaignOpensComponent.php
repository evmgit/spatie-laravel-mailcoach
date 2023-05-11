<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Queries\CampaignOpensQuery;
use Spatie\Mailcoach\MainNavigation;

class CampaignOpensComponent extends DataTableComponent
{
    public string $sort = '-first_opened_at';

    public Campaign $campaign;

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;

        app(MainNavigation::class)->activeSection()?->add($this->campaign->name, route('mailcoach.campaigns'));
    }

    public function getTitle(): string
    {
        return __mc('Opens');
    }

    public function getView(): string
    {
        return 'mailcoach::app.campaigns.opens';
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
        $campaignOpens = (new CampaignOpensQuery($this->campaign, $request));

        return [
            'campaign' => $this->campaign,
            'mailOpens' => $campaignOpens->paginate($request->per_page),
            'totalMailOpensCount' => $campaignOpens->paginate($request->per_page)->total(),
        ];
    }
}
