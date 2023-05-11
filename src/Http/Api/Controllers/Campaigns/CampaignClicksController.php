<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Resources\CampaignClickResource;
use Spatie\Mailcoach\Http\App\Queries\CampaignLinksQuery;

class CampaignClicksController
{
    use AuthorizesRequests;

    public function __invoke(Campaign $campaign)
    {
        $this->authorize('view', $campaign);

        $campaignLinks = new CampaignLinksQuery($campaign);

        return CampaignClickResource::collection($campaignLinks->paginate());
    }
}
