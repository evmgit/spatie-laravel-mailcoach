<?php

namespace Spatie\Mailcoach\Domain\Automation\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Trigger;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\TriggeredBySchedule;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class RunAutomationTriggersCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:run-automation-triggers';

    public $description = 'Run all triggers for automations';

    public function handle()
    {
        $this->comment('Start running triggers...');

        static::getAutomationTriggerClass()::query()
            ->whereHas('automation', function (Builder $query) {
                $query
                    ->whereHas('actions')
                    ->where('status', AutomationStatus::STARTED);
            })
            ->cursor()
            ->each(function (Trigger $trigger) {
                /** @var \Spatie\Mailcoach\Domain\Automation\Support\Triggers\AutomationTrigger $automationTrigger */
                $automationTrigger = $trigger->trigger;

                if (! $automationTrigger instanceof TriggeredBySchedule) {
                    return;
                }

                $this->info("Triggering automation id `{$trigger->automation->id}`");
                $automationTrigger
                    ->setAutomation($trigger->automation)
                    ->trigger();
            });

        $this->comment('All done!');
    }
}
