<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Queries\CampaignOpensQuery;

class CampaignOpensController
{
    use AuthorizesRequests;

    public function __invoke(Campaign $campaign)
    {
        $this->authorize('view', $campaign);

        $campaignOpensQuery = new CampaignOpensQuery($campaign);

        return view('mailcoach::app.campaigns.opens', [
            'campaign' => $campaign,
            'campaignOpens' => $campaignOpensQuery->paginate(),
            'totalCampaignOpensCount' => $campaignOpensQuery->totalCount,
        ]);
    }
}
