<?php

namespace Spatie\Mailcoach\Domain\Automation\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailToSubscriberAction;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Mailcoach;

class SendAutomationMailToSubscriberJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public AutomationMail $automationMail;

    public ActionSubscriber $actionSubscriber;

    /** @var string */
    public $queue;

    public function __construct(AutomationMail $automationMail, ActionSubscriber $actionSubscriber)
    {
        $this->automationMail = $automationMail;

        $this->actionSubscriber = $actionSubscriber;

        $this->queue = config('mailcoach.automation.perform_on_queue.send_automation_mail_to_subscriber_job');

        $this->connection = $this->connection ?? Mailcoach::getQueueConnection();
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailToSubscriberAction $sendAutomationMailToSubscriberAction */
        $sendAutomationMailToSubscriberAction = Mailcoach::getAutomationActionClass('send_automation_mail_to_subscriber', SendAutomationMailToSubscriberAction::class);
        $sendAutomationMailToSubscriberAction->execute($this->automationMail, $this->actionSubscriber);
    }
}
