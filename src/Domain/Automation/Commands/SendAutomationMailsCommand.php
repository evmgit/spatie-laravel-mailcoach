<?php

namespace Spatie\Mailcoach\Domain\Automation\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailsJob;

class SendAutomationMailsCommand extends Command
{
    public $signature = 'mailcoach:send-automation-mails';

    public $description = 'Send pending automation mails.';

    public function handle()
    {
        dispatch(new SendAutomationMailsJob());
    }
}
