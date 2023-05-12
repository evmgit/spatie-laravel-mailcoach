<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Swift_Message;

class SendAutomationMailTestAction
{
    public function execute(AutomationMail $mail, string $email): void
    {
        $html = $mail->htmlWithInlinedCss();

        $convertHtmlToTextAction = Config::getAutomationActionClass('convert_html_to_text', ConvertHtmlToTextAction::class);

        $text = $convertHtmlToTextAction->execute($html);

        $subscriber = Subscriber::make();

        $mailable = resolve(MailcoachMail::class)
            ->setFrom($mail->fromEmail($subscriber), $mail->fromName($subscriber))
            ->setHtmlContent($html)
            ->setTextContent($text)
            ->setHtmlView('mailcoach::mails.automation.automationHtml')
            ->setTextView('mailcoach::mails.automation.automationText')
            ->subject("[Test] {$mail->subject}")
            ->withSwiftMessage(function (Swift_Message $message) {
                $message->getHeaders()->addTextHeader('X-MAILCOACH', 'true');
                $message->getHeaders()->addTextHeader('X-Entity-Ref-ID', Str::uuid()->toString());
            });

        if ($mail->reply_to_email) {
            $mailable->setReplyTo($mail->reply_to_email, $mail->reply_to_name);
        }

        $mailer = config('mailcoach.automation.mailer')
            ?? config('mailcoach.mailer')
            ?? config('mail.default');

        Mail::mailer($mailer)
            ->to($email)
            ->send($mailable);
    }
}
