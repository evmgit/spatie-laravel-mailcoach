<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class UnscheduleCampaignController
{
    use AuthorizesRequests;

    public function __invoke(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $campaign->markAsUnscheduled();

        flash()->success(__('Campaign :campaign was unscheduled', ['campaign' => $campaign->name]));

        return redirect()->route('mailcoach.campaigns.delivery', $campaign->id);
    }
}
