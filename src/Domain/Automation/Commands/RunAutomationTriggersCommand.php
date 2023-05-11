<?php

namespace Spatie\Mailcoach\Domain\Automation\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunAutomationTriggersJob;

class RunAutomationTriggersCommand extends Command
{
    public $signature = 'mailcoach:run-automation-triggers';

    public $description = 'Run all triggers for automations';

    public function handle()
    {
        dispatch(new RunAutomationTriggersJob());
    }
}
