<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\ViewModels\CampaignSummaryViewModel;

class CampaignSummaryController
{
    use AuthorizesRequests;

    public function __invoke(Campaign $campaign)
    {
        $this->authorize('view', $campaign);

        $viewModel = new CampaignSummaryViewModel($campaign);

        return view('mailcoach::app.campaigns.summary', $viewModel);
    }
}
