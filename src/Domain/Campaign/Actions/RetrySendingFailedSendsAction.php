<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class RetrySendingFailedSendsAction
{
    public function execute(Campaign $campaign): int
    {
        $failedSendsCount = $campaign->sends()->getQuery()->failed()->update([
            'sent_at' => null,
            'failed_at' => null,
            'failure_reason' => null,
        ]);

        $campaign->sends()->getQuery()->pending()->each(function (Send $pendingSend) {
            dispatch(new SendCampaignMailJob($pendingSend));
        });

        return $failedSendsCount;
    }
}
