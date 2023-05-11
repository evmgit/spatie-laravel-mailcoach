<?php

namespace Spatie\Mailcoach\Domain\Campaign\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailsJob;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SendCampaignMailsCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:send-campaign-mails';

    public $description = 'Send scheduled campaigns.';

    public function handle()
    {
        dispatch(new SendCampaignMailsJob());
    }
}
