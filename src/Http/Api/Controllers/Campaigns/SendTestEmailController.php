<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\SendCampaignTestRequest;

class SendTestEmailController
{
    use AuthorizesRequests;
    use RespondsToApiRequests;

    public function __invoke(SendCampaignTestRequest $request, Campaign $campaign)
    {
        $this->authorize("view", $campaign);

        $campaign->sendTestMail($request->sanitizedEmails());

        return $this->respondOk();
    }
}
