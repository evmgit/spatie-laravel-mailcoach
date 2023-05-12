<?php

namespace Spatie\Mailcoach\Domain\Campaign\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SendScheduledCampaignsCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:send-scheduled-campaigns';

    public $description = 'Send scheduled campaigns.';

    public function handle()
    {
        $this->comment('Checking if there are scheduled campaigns that should be sent...');

        $this->getCampaignClass()::shouldBeSentNow()
            ->each(function (Campaign $campaign) {
                $this->info("Sending campaign `{$campaign->name}` ({$campaign->id})...");
                $campaign->update(['scheduled_at' => null]);
                $campaign->send();
            });

        $this->comment('All done!');
    }
}
