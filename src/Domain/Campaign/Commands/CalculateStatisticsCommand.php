<?php

namespace Spatie\Mailcoach\Domain\Campaign\Commands;

use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Spatie\Mailcoach\Domain\Campaign\Jobs\CalculateCampaignStatisticsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CalculateStatisticsCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:calculate-statistics {campaignId?}';

    public $description = 'Calculate the statistics of the recently sent campaigns';

    protected CarbonInterface $now;

    public function handle()
    {
        Cache::put('mailcoach-last-schedule-run', now());

        $this->comment('Start calculating statistics...');

        $campaignId = $this->argument('campaignId');

        $campaignId
            ? dispatch_sync(new CalculateStatisticsJob(self::getCampaignClass()::find($campaignId)))
            : dispatch(new CalculateCampaignStatisticsJob());

        $this->comment('All done!');
    }
}
