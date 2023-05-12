<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Resources\CampaignUnsubscribeResource;
use Spatie\Mailcoach\Http\App\Queries\CampaignUnsubscribesQuery;

class CampaignUnsubscribesController
{
    use AuthorizesRequests;

    public function __invoke(Campaign $campaign)
    {
        $this->authorize("view", $campaign);

        $unsubscribes = (new CampaignUnsubscribesQuery($campaign));

        return CampaignUnsubscribeResource::collection($unsubscribes->paginate());
    }
}
