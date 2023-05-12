<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Requests\Campaigns\ScheduleCampaignRequest;

class ScheduleCampaignController
{
    use AuthorizesRequests;

    public function __invoke(Campaign $campaign, ScheduleCampaignRequest $request)
    {
        $this->authorize('update', $campaign);

        if (! $campaign->isPending()) {
            flash()->error(__('Campaign :campaign could not be scheduled because it has already been sent.', ['campaign' => $campaign->name]));

            return back();
        }

        $campaign->scheduleToBeSentAt($request->getScheduledAt());

        flash()->success(__('Campaign :campaign is scheduled for delivery.', ['campaign' => $campaign->name]));

        return redirect()->route('mailcoach.campaigns.delivery', $campaign->id);
    }
}
