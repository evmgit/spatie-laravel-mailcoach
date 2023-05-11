<?php

namespace Spatie\Mailcoach\Domain\Shared\Mails;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Symfony\Component\Mime\Email;

class MailcoachMail extends Mailable
{
    use SerializesModels;

    public ?Sendable $sendable = null;

    public ?Send $send = null;

    public string $htmlContent = '';

    public string $textContent = '';

    public ?string $fromEmail = null;

    public ?string $fromName = null;

    public ?string $replyToEmail = null;

    public ?string $replyToName = null;

    public $htmlView = null;

    public $textView = null;

    public function setSend(Send $send): static
    {
        $this->send = $send;

        $this->sendable = $send->campaign;

        return $this;
    }

    public function setFrom(string $fromEmail, string $fromName = null): static
    {
        $this->fromEmail = $fromEmail;

        $this->fromName = $fromName;

        return $this;
    }

    public function setReplyTo(string $replyToEmail, string $replyToName = null): static
    {
        $this->replyToEmail = $replyToEmail;

        $this->replyToName = $replyToName;

        return $this;
    }

    public function setHtmlView(string $htmlView): static
    {
        $this->htmlView = $htmlView;

        return $this;
    }

    public function setTextView(string $textView): static
    {
        $this->textView = $textView;

        return $this;
    }

    public function setSendable(Sendable $sendable): static
    {
        $this->sendable = $sendable;

        $this->setFrom(
            $sendable->getFromEmail($this->send),
            $sendable->getFromName($this->send),
        );

        $replyTo = $sendable->getReplyToEmail($this->send);

        if ($replyTo) {
            $replyToName = $sendable->getReplyToName($this->send);
            $this->setReplyTo($replyTo, $replyToName);
        }

        $htmlView = match (true) {
            $sendable instanceof AutomationMail => 'mailcoach::mails.automation.automationHtml',
            $sendable instanceof Campaign => 'mailcoach::mails.campaignHtml',
        };

        $textView = match (true) {
            $sendable instanceof AutomationMail => 'mailcoach::mails.automation.automationText',
            $sendable instanceof Campaign => 'mailcoach::mails.campaignText',
        };

        $this
            ->setHtmlView($htmlView)
            ->setTextView($textView);

        return $this;
    }

    public function setHtmlContent(string $htmlContent = ''): static
    {
        $this->htmlContent = $htmlContent;

        return $this;
    }

    public function setTextContent(string $textContent): static
    {
        $this->textContent = $textContent;

        return $this;
    }

    public function subject($subject): static
    {
        if (! empty($this->subject)) {
            return $this;
        }

        $this->subject = $subject;

        return $this;
    }

    public function build()
    {
        $mail = $this
            ->from($this->fromEmail, $this->fromName)
            ->subject($this->subject)
            ->view($this->htmlView)
            ->text($this->textView)
            ->addUnsubscribeHeaders()
            ->storeTransportMessageId();

        if ($this->replyToEmail) {
            $mail->replyTo($this->replyToEmail, $this->replyToName);
        }

        return $mail;
    }

    protected function addUnsubscribeHeaders(): static
    {
        if (is_null($this->send)) {
            return $this;
        }

        $this->withSymfonyMessage(function (Email $message) {
            $message
                ->getHeaders()
                ->addTextHeader(
                    'List-Unsubscribe',
                    '<'.$this->send->subscriber->unsubscribeUrl($this->send).'>'
                );

            $message
                ->getHeaders()
                ->addTextHeader(
                    'List-Unsubscribe-Post',
                    'List-Unsubscribe=One-Click'
                );

            $message
                ->getHeaders()
                ->addTextHeader(
                    'mailcoach-send-uuid',
                    $this->send->uuid
                );
        });

        return $this;
    }

    protected function storeTransportMessageId(): static
    {
        if (is_null($this->send)) {
            return $this;
        }

        $this->withSymfonyMessage(function (Email $message) {
            $messageId = $message->generateMessageId();
            $message->getHeaders()->addIdHeader('Message-ID', $messageId);
            $this->send->storeTransportMessageId($messageId);
        });

        return $this;
    }
}
