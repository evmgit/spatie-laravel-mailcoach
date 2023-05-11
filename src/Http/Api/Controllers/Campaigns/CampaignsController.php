<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Actions\UpdateCampaignAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\CampaignRequest;
use Spatie\Mailcoach\Http\Api\Resources\CampaignResource;
use Spatie\Mailcoach\Http\App\Queries\CampaignsQuery;

class CampaignsController
{
    use AuthorizesRequests;
    use UsesMailcoachModels;
    use RespondsToApiRequests;

    public function index(CampaignsQuery $campaigns)
    {
        $this->authorize('viewAny', static::getCampaignClass());

        return CampaignResource::collection($campaigns->with('emailList')->paginate());
    }

    public function show(Campaign $campaign)
    {
        $this->authorize('viewAny', static::getCampaignClass());

        return new CampaignResource($campaign);
    }

    public function store(
        CampaignRequest $request,
        UpdateCampaignAction $updateCampaignAction
    ) {
        $this->authorize('create', static::getCampaignClass());

        $campaignClass = self::getCampaignClass();

        $campaign = new $campaignClass;

        $campaign = $updateCampaignAction->execute(
            $campaign,
            $request->validated(),
            $request->template()
        );

        return new CampaignResource($campaign);
    }

    public function update(
        Campaign $campaign,
        CampaignRequest $request,
        UpdateCampaignAction $updateCampaignAction
    ) {
        $this->authorize('update', $campaign);

        $campaign = $updateCampaignAction->execute(
            $campaign,
            $request->validated(),
            $request->template(),
        );

        return new CampaignResource($campaign);
    }

    public function destroy(Campaign $campaign)
    {
        $this->authorize('delete', $campaign);

        $campaign->delete();

        return $this->respondOk();
    }
}
