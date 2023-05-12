<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CancelSendingCampaignController
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public function __invoke(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $batch = Bus::findBatch(
            $campaign->send_batch_id
        );

        $batch->cancel();

        $campaign->update([
            'status' => CampaignStatus::CANCELLED,
            'sent_at' => now(),
        ]);

        flash()->success(__('Sending successfully cancelled.'));

        return redirect()->back();
    }
}
