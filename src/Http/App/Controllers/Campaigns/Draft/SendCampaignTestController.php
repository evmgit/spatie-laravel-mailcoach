<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Carbon\CarbonInterval;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Requests\Campaigns\SendCampaignTestRequest;

class SendCampaignTestController
{
    use AuthorizesRequests;

    public function __invoke(Campaign $campaign, SendCampaignTestRequest $request)
    {
        $this->authorize('view', $campaign);

        if (! $campaign->isPending()) {
            flash()->error(__("Cannot send a test email for campaign :campaign because it has already been sent.", ['campaign' => $campaign->name]));

            return back();
        }

        cache()->put('mailcoach-test-email-addresses', $request->emails, (int)CarbonInterval::month()->totalSeconds);

        $campaign->sendTestMail($request->sanitizedEmails());

        $this->flashSuccessMessage($request);

        return back();
    }

    protected function flashSuccessMessage(SendCampaignTestRequest $request): void
    {
        if (count($request->sanitizedEmails()) > 1) {
            $emailCount = count($request->sanitizedEmails());

            flash()->success(__('A test email was sent to :count addresses.', ['count' => $emailCount]));

            return;
        }

        flash()->success(__('A test email was sent to :email.', ['email' => $request->sanitizedEmails()[0]]));
    }
}
