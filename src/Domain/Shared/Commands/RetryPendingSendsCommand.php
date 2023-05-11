<?php

namespace Spatie\Mailcoach\Domain\Shared\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailJob;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class RetryPendingSendsCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:retry-pending-sends';

    public $description = 'Dispatch a job for each MailSend that has not been sent yet';

    public function handle()
    {
        $pendingSendCount = self::getSendClass()::whereNull('sent_at')->count();

        $this->comment("Dispatching jobs for {$pendingSendCount} pending Sends");

        self::getSendClass()::query()
            ->whereNull('sent_at')
            ->whereNotNull('campaign_id')
            ->each(function (Send $send) {
                dispatch(new SendCampaignMailJob($send));
            });

        self::getSendClass()::query()
            ->whereNull('sent_at')
            ->whereNotNull('automation_mail_id')
            ->each(function (Send $send) {
                dispatch(new SendAutomationMailJob($send));
            });

        $this->comment('All done!');
    }
}
