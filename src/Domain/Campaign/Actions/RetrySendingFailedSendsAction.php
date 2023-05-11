<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class RetrySendingFailedSendsAction
{
    public function execute(Campaign $campaign): int
    {
        $sendIds = $campaign->sends()->getQuery()->failed()->pluck('id');

        $failedSendsCount = $campaign->sends()->getQuery()->failed()->update([
            'sent_at' => null,
            'failed_at' => null,
            'failure_reason' => null,
            'sending_job_dispatched_at' => now(),
        ]);

        $campaign->sends()->getQuery()->whereIn('id', $sendIds)->each(function (Send $pendingSend) {
            dispatch(new SendCampaignMailJob($pendingSend));
        });

        return $failedSendsCount;
    }
}
