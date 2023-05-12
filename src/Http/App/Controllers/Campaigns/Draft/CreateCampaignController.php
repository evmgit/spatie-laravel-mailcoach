<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Actions\UpdateCampaignAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Requests\Campaigns\StoreCampaignRequest;

class CreateCampaignController
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public function __invoke(
        StoreCampaignRequest $request,
        UpdateCampaignAction $updateCampaignAction
    ) {
        $campaignClass = static::getCampaignClass();

        $this->authorize("create", $campaignClass);

        $campaign = new $campaignClass;

        $campaign = $updateCampaignAction->execute(
            $campaign,
            $request->validated(),
            $request->template()
        );

        flash()->success(__('Campaign :campaign was created.', ['campaign' => $campaign->name]));

        return redirect()->route('mailcoach.campaigns.settings', $campaign);
    }
}
