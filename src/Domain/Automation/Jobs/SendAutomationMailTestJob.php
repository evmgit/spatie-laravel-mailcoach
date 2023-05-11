<?php

namespace Spatie\Mailcoach\Domain\Automation\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailTestAction;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Mailcoach;

class SendAutomationMailTestJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public AutomationMail $mail;

    public string $email;

    /** @var string */
    public $queue;

    public function __construct(AutomationMail $mail, string $email)
    {
        $this->mail = $mail;

        $this->email = $email;

        $this->queue = config('mailcoach.automation.perform_on_queue.send_test_mail_job');

        $this->connection = $this->connection ?? Mailcoach::getQueueConnection();
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailTestAction $sendTestMailAction */
        $sendTestMailAction = Mailcoach::getAutomationActionClass('send_test_mail', SendAutomationMailTestAction::class);

        $sendTestMailAction->execute($this->mail, $this->email);
    }
}
