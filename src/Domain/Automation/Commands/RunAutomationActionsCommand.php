<?php

namespace Spatie\Mailcoach\Domain\Automation\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunAutomationActionsJob;

class RunAutomationActionsCommand extends Command
{
    public $signature = 'mailcoach:run-automation-actions';

    public $description = 'Run all registered actions for automations';

    public function handle()
    {
        dispatch(new RunAutomationActionsJob());
    }
}
