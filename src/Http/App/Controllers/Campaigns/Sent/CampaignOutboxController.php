<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Queries\CampaignSendsQuery;

class CampaignOutboxController
{
    use AuthorizesRequests;

    public function __invoke(Campaign $campaign)
    {
        $this->authorize('view', $campaign);

        $sendsQuery = new CampaignSendsQuery($campaign);

        return view('mailcoach::app.campaigns.outbox', [
            'campaign' => $campaign,
            'sends' => $sendsQuery->paginate(),
            'totalSends' => $campaign->sends()->count(),
            'totalPending' => $campaign->sends()->pending()->count(),
            'totalSent' => $campaign->sends()->sent()->count(),
            'totalFailed' => $campaign->sends()->failed()->count(),
            'totalBounces' => $campaign->sends()->bounced()->count(),
            'totalComplaints' => $campaign->sends()->complained()->count(),
        ]);
    }
}
