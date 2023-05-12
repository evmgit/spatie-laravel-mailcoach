<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class DestroyCampaignController
{
    use AuthorizesRequests;

    public function __invoke(Campaign $campaign)
    {
        $this->authorize('delete', $campaign);

        $campaign->delete();

        flash()->success(__('Campaign :campaign was deleted.', ['campaign' => $campaign->name]));

        return redirect()->back();
    }
}
