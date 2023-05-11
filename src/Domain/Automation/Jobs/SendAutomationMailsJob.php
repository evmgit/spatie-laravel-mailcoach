<?php

namespace Spatie\Mailcoach\Domain\Automation\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailsAction;
use Spatie\Mailcoach\Domain\Automation\Exceptions\SendAutomationMailsTimeLimitApproaching;
use Spatie\Mailcoach\Mailcoach;

class SendAutomationMailsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $uniqueFor;

    public function __construct()
    {
        $this->uniqueFor = max(60, config('mailcoach.automation.send_automation_mails_maximum_job_runtime_in_seconds'));
        $this->onQueue(config('mailcoach.shared.perform_on_queue.schedule'));
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailsAction $sendAutomationMailsAction */
        $sendAutomationMailsAction = Mailcoach::getAutomationActionClass('send_automation_mails_action', SendAutomationMailsAction::class);

        $stopExecutingAt = now()->addSeconds($this->uniqueFor);

        try {
            $sendAutomationMailsAction->execute($stopExecutingAt);
        } catch (SendAutomationMailsTimeLimitApproaching) {
            return;
        }
    }
}
