<?php

namespace Spatie\Mailcoach\Domain\Automation\Commands;

use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CalculateAutomationMailStatisticsCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:calculate-automation-mail-statistics {automationMailId?}';

    public $description = 'Calculate the statistics of automation mails';

    protected CarbonInterface $now;

    public function handle()
    {
        Cache::put('mailcoach-last-schedule-run', now());

        $this->comment('Start calculating statistics...');

        $automationMailId = $this->argument('automationMailId');

        $automationMailId
            ? dispatch_now(new CalculateStatisticsJob($this->getAutomationMailClass()::find($automationMailId)))
            : $this->calculateStatisticsOfAutomationMails();

        $this->comment('All done!');
    }

    protected function calculateStatisticsOfAutomationMails(): void
    {
        $this->now = now();

        static::getAutomationClass()::query()
            ->where('status', AutomationStatus::STARTED)
            ->get()
            ->flatMap(function (Automation $automation) {
                return $automation->allActions;
            })->filter(function (Action $action) {
                return $action->action::class === SendAutomationMailAction::class;
            })->map(function (Action $action) {
                return $action->action->automationMail;
            })->each(function (AutomationMail $automationMail) {
                $this->info("Calculating statistics for automation mail id {$automationMail->id}...");

                $automationMail->dispatchCalculateStatistics();
            });
    }
}
