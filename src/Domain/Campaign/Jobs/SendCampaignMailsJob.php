<?php

namespace Spatie\Mailcoach\Domain\Campaign\Jobs;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignMailsAction;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\SendCampaignTimeLimitApproaching;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class SendCampaignMailsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UsesMailcoachModels;

    public function __construct()
    {
        $this->onQueue(config('mailcoach.shared.perform_on_queue.schedule'));
    }

    public function uniqueFor(): int
    {
        return max(60, config('mailcoach.campaigns.send_campaign_maximum_job_runtime_in_seconds'));
    }

    public function retryUntil(): CarbonInterface
    {
        return now()->addHour();
    }

    public function handle(): void
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignMailsAction $sendCampaignMailsAction */
        $sendCampaignMailsAction = Mailcoach::getCampaignActionClass('send_campaign_mails', SendCampaignMailsAction::class);

        $maxRuntimeInSeconds = max(60, config('mailcoach.campaigns.send_campaign_maximum_job_runtime_in_seconds'));

        self::getCampaignClass()::query()
            ->sending()
            ->each(function (Campaign $campaign) use ($sendCampaignMailsAction, $maxRuntimeInSeconds) {
                $stopExecutingAt = now()->addSeconds($maxRuntimeInSeconds);

                try {
                    $sendCampaignMailsAction->execute($campaign, $stopExecutingAt);
                } catch (SendCampaignTimeLimitApproaching) {
                    return;
                }
            });
    }
}
