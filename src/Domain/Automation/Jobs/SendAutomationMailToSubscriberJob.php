<?php

namespace Spatie\Mailcoach\Domain\Automation\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailToSubscriberAction;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Support\Config;

class SendAutomationMailToSubscriberJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public AutomationMail $automationMail;

    public Subscriber $subscriber;

    /** @var string */
    public $queue;

    public function __construct(AutomationMail $automationMail, Subscriber $subscriber)
    {
        $this->automationMail = $automationMail;

        $this->subscriber = $subscriber;

        $this->queue = config('mailcoach.automation.perform_on_queue.send_automation_mail_to_subscriber_job');

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailToSubscriberAction $sendAutomationMailToSubscriberAction */
        $sendAutomationMailToSubscriberAction = Config::getAutomationActionClass('send_automation_mail_to_subscriber', SendAutomationMailToSubscriberAction::class);
        $sendAutomationMailToSubscriberAction->execute($this->automationMail, $this->subscriber);
    }
}
