<?php

namespace Spatie\Mailcoach\Domain\Automation\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Automation\Jobs\CalculateAutomationMailStatisticsJob;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CalculateAutomationMailStatisticsCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:calculate-automation-mail-statistics {automationMailId?}';

    public $description = 'Calculate the statistics of automation mails';

    public function handle()
    {
        dispatch(new CalculateAutomationMailStatisticsJob($this->argument('automationMailId')));
    }
}
