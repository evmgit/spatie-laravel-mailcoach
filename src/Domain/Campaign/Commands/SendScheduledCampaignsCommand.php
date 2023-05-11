<?php

namespace Spatie\Mailcoach\Domain\Campaign\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendScheduledCampaignsJob;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SendScheduledCampaignsCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:send-scheduled-campaigns';

    public $description = 'Send scheduled campaigns.';

    public function handle()
    {
        dispatch(new SendScheduledCampaignsJob());
    }
}
