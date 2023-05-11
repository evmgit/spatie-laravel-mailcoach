<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Resources\CampaignOpenResource;
use Spatie\Mailcoach\Http\App\Queries\CampaignOpensQuery;

class CampaignOpensController
{
    use AuthorizesRequests;

    public function __invoke(Campaign $campaign)
    {
        $this->authorize('view', $campaign);

        $campaignOpens = new CampaignOpensQuery($campaign);

        return CampaignOpenResource::collection($campaignOpens->paginate());
    }
}
