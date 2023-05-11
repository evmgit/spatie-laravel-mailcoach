<?php

namespace Spatie\Mailcoach\Domain\Campaign\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignSummaryMailJob;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SendCampaignSummaryMailCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:send-campaign-summary-mail';

    public $description = 'Send a summary mail to campaigns that have been sent out recently';

    public function handle()
    {
        dispatch(new SendCampaignSummaryMailJob());
    }
}
