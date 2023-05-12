<?php

namespace Spatie\Mailcoach\Domain\Automation\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunAutomationActionJob;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class RunAutomationActionsCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:run-automation-actions';

    public $description = 'Run all registered actions for automations';

    public function handle()
    {
        $this->comment('Start running actions...');

        static::getAutomationClass()::query()
            ->where('status', AutomationStatus::STARTED)
            ->cursor()
            ->each(function (Automation $automation) {
                if (! is_null($automation->run_at) && $automation->run_at->add($automation->interval)->isFuture()) {
                    return;
                }

                $this->info("Dispatching jobs for all actions for automation id `{$automation->id}`");

                $automation->allActions()->each(function (Action $action) {
                    $this->info("Dispatching job for action `{$action->id}`");

                    return dispatch(new RunAutomationActionJob($action));
                });

                $automation->update(['run_at' => now()]);
            });

        $this->comment('All done!');
    }
}
