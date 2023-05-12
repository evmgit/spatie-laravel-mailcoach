<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class CampaignDeliveryController
{
    public function __invoke(Campaign $campaign)
    {
        return view('mailcoach::app.campaigns.delivery', [
            'campaign' => $campaign,
            'links' => $campaign->htmlLinks(),
        ]);
    }
}
