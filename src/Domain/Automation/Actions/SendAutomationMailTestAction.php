<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class SendAutomationMailTestAction
{
    use UsesMailcoachModels;

    public function __construct(
        private SendMailAction $sendMailAction
    ) {
    }

    public function execute(AutomationMail $mail, string $email): void
    {
        $subject = $mail->subject;

        /** @var \Spatie\Mailcoach\Domain\Automation\Actions\PrepareSubjectAction $prepareSubjectAction */
        $prepareSubjectAction = Mailcoach::getAutomationActionClass('prepare_subject', PrepareSubjectAction::class);
        $prepareSubjectAction->execute($mail);

        /** @var \Spatie\Mailcoach\Domain\Automation\Actions\PrepareEmailHtmlAction $prepareEmailHtmlAction */
        $prepareEmailHtmlAction = Mailcoach::getAutomationActionClass('prepare_email_html', PrepareEmailHtmlAction::class);
        $prepareEmailHtmlAction->execute($mail);

        /** @var \Spatie\Mailcoach\Domain\Automation\Actions\PrepareWebviewHtmlAction $prepareWebviewHtmlAction */
        $prepareWebviewHtmlAction = Mailcoach::getAutomationActionClass('prepare_webview_html', PrepareWebviewHtmlAction::class);
        $prepareWebviewHtmlAction->execute($mail);

        $mail->subject = "[Test] {$subject}";

        if (! $subscriber = self::getSubscriberClass()::where('email', $email)->first()) {
            $subscriber = self::getSubscriberClass()::make([
                'uuid' => Str::uuid()->toString(),
                'email' => $email,
            ]);
        }

        $send = self::getSendClass()::make([
            'uuid' => Str::uuid()->toString(),
            'subscriber_id' => $subscriber->id,
            'automation_mail_id' => $mail->id,
        ]);
        $send->setRelation('subscriber', $subscriber);
        $send->setRelation('automationMail', $mail);

        try {
            $this->sendMailAction->execute($send, isTest: true);
        } finally {
            $mail->update(['subject' => $subject]);
            $send->delete();
        }
    }
}
