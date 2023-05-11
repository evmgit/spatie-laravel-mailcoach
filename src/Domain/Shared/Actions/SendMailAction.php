<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Automation\Actions\ConvertHtmlToTextAction as AutomationMailConvertHtmlToTextAction;
use Spatie\Mailcoach\Domain\Automation\Actions\PersonalizeHtmlAction as AutomationMailPersonalizeHtmlAction;
use Spatie\Mailcoach\Domain\Automation\Actions\PersonalizeSubjectAction as AutomationMailPersonalizeSubjectAction;
use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailSentEvent;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Actions\ConvertHtmlToTextAction as CampaignConvertHtmlToTextAction;
use Spatie\Mailcoach\Domain\Campaign\Actions\PersonalizeHtmlAction as CampaignPersonalizeHtmlAction;
use Spatie\Mailcoach\Domain\Campaign\Actions\PersonalizeSubjectAction as CampaignPersonalizeSubjectAction;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignMailSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Mailcoach;
use Symfony\Component\Mime\Email;
use Throwable;

class SendMailAction
{
    public function execute(Send $pendingSend, bool $isTest = false): void
    {
        try {
            $this->sendMail($pendingSend, $isTest);
        } catch (Throwable $exception) {
            if (! $isTest) {
                /**
                 * Postmark returns code 406 when you try to send
                 * to an email that has been marked as inactive
                 */
                if (str_contains($exception->getMessage(), '(code 406)')) {
                    // Mark as bounced
                    $pendingSend->markAsSent();
                    $pendingSend->registerBounce();

                    return;
                }
                report($exception);

                $pendingSend->markAsFailed($exception->getMessage());
            }
        }
    }

    protected function sendMail(Send $pendingSend, bool $isTest = false): void
    {
        if ($pendingSend->wasAlreadySent()) {
            return;
        }

        $sendable = $pendingSend->getSendable();

        if (! $sendable) {
            $pendingSend->delete();

            return;
        }

        /** @var AutomationMailPersonalizeSubjectAction|CampaignPersonalizeSubjectAction $personalizeSubjectAction */
        $personalizeSubjectAction = match (true) {
            $sendable instanceof AutomationMail => Mailcoach::getAutomationActionClass('personalize_subject', AutomationMailPersonalizeSubjectAction::class),
            $sendable instanceof Campaign => Mailcoach::getCampaignActionClass('personalize_subject', CampaignPersonalizeSubjectAction::class),
        };

        $personalisedSubject = $personalizeSubjectAction->execute($sendable->subject, $pendingSend);

        /** @var AutomationMailPersonalizeHtmlAction|CampaignPersonalizeHtmlAction $personalizeHtmlAction */
        $personalizeHtmlAction = match (true) {
            $sendable instanceof AutomationMail => Mailcoach::getAutomationActionClass('personalize_html', AutomationMailPersonalizeHtmlAction::class),
            $sendable instanceof Campaign => Mailcoach::getCampaignActionClass('personalize_html', CampaignPersonalizeHtmlAction::class),
        };

        $personalisedHtml = $personalizeHtmlAction->execute(
            $sendable->email_html,
            $pendingSend,
        );

        /** @var AutomationMailConvertHtmlToTextAction|CampaignConvertHtmlToTextAction $convertHtmlToTextAction */
        $convertHtmlToTextAction = match (true) {
            $sendable instanceof AutomationMail => Mailcoach::getAutomationActionClass('convert_html_to_text', AutomationMailConvertHtmlToTextAction::class),
            $sendable instanceof Campaign => Mailcoach::getCampaignActionClass('convert_html_to_text', CampaignConvertHtmlToTextAction::class),
        };

        $personalisedText = $convertHtmlToTextAction->execute($personalisedHtml);

        $mailcoachMail = resolve(MailcoachMail::class);

        $mailcoachMail
            ->setSend($pendingSend)
            ->setSendable($sendable)
            ->subject($personalisedSubject)
            ->setHtmlContent($personalisedHtml)
            ->setTextContent($personalisedText)
            ->withSymfonyMessage(function (Email $message) use ($pendingSend) {
                $message->getHeaders()->addTextHeader('X-MAILCOACH', 'true');
                $message->getHeaders()->addTextHeader('Precedence', 'Bulk');

                /** Postmark specific header */
                $message->getHeaders()->addTextHeader('X-PM-Metadata-send-uuid', $pendingSend->uuid);
            });

        Mail::mailer($pendingSend->getMailerKey())
            ->to($pendingSend->subscriber->email)
            ->send($mailcoachMail);

        $pendingSend->markAsSent();

        if (! $isTest) {
            match (true) {
                $sendable instanceof AutomationMail => event(new AutomationMailSentEvent($pendingSend)),
                $sendable instanceof Campaign => event(new CampaignMailSentEvent($pendingSend)),
            };
        }
    }
}
