<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class SendCampaignController
{
    use AuthorizesRequests;

    public function __invoke(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        if (! $campaign->isPending()) {
            flash()->error(__('Campaign :campaign could not be sent because it has already been sent.', ['campaign' => $campaign->name]));

            return back();
        }

        $campaign->send();

        flash()->success(__('Campaign :campaign is being sent.', ['campaign' => $campaign->name]));

        return redirect()->route('mailcoach.campaigns.summary', $campaign->id);
    }
}
