<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Mails;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\StoresMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\UsesMailcoachTemplate;
use Spatie\Mailcoach\Mailcoach;
use Symfony\Component\Mime\Email;

class TransactionalMail extends Mailable
{
    use SerializesModels;
    use StoresMail;
    use UsesMailcoachTemplate;

    private ?string $mailName;

    private array $replacements;

    private array $embeddedAttachments;

    private array $attachedAttachments;

    private array $fields;

    private ?string $originalHtml;

    public function __construct(
        ?string $mailName,
        string $subject,
        array|string $from,
        array $to,
        array $cc = [],
        array $bcc = [],
        array $replyTo = [],
        string $mailer = null,
        array $replacements = [],
        array $attachments = [],
        bool $store = true,
        ?string $html = null,
    ) {
        $this->mailName = $mailName;
        $this->replacements = $replacements;
        $this->originalHtml = $html;

        $this
            ->setTransactionalHeader()
            ->prepareAttachment($attachments)
            ->prepareHtml($html);

        $this
            ->when($store, fn (TransactionalMail $mail) => $mail->store())
            ->from($from)
            ->to($to)
            ->cc($cc)
            ->bcc($bcc)
            ->replyTo($replyTo)
            ->subject($subject)
            ->mailer($mailer ?? Mailcoach::defaultTransactionalMailer())
            ->view('mailcoach::mails.transactionalMails.mail');
    }

    public function build()
    {
        if ($this->shouldUseMailcoachTemplate()) {
            $this->html = null;

            $this->template(
                $this->mailName,
                $this->replacements,
            );
        } else {
            $this->view('mailcoach::mails.transactionalMails.template', ['content' => $this->html]);
        }

        $this->withSymfonyMessage(function (Email $email) {
            foreach ($this->embeddedAttachments as $embeddedAttachment) {
                $email->embed(
                    body: base64_decode($embeddedAttachment['content']),
                    name: $embeddedAttachment['name'],
                    contentType: $embeddedAttachment['content_type'],
                );
            }

            foreach ($this->attachedAttachments as $attachedAttachment) {
                $email->attach(
                    body: base64_decode($attachedAttachment['content']),
                    name: $attachedAttachment['name'],
                    contentType: $attachedAttachment['content_type'],
                );
            }
        });
    }

    protected function prepareAttachment(array $attachments): self
    {
        $this->embeddedAttachments = array_filter(
            $attachments,
            fn ($attachment) => ! is_null($attachment['content_id'] ?? null),
        );

        $this->attachedAttachments = array_filter(
            $attachments,
            fn ($attachment) => is_null($attachment['content_id'] ?? null),
        );

        return $this;
    }

    protected function prepareHtml(?string $html): self
    {
        if ($this->shouldUseMailcoachTemplate()) {
            return $this;
        }

        $this->html = $html;

        if (! str_contains($html, '<html')) {
            $this->html = "<html><body>{$this->html}</body></html>";
        }

        return $this;
    }

    protected function shouldUseMailcoachTemplate(): bool
    {
        if ($this->originalHtml === 'use-mailcoach-mail') {
            return true;
        }

        if (! $this->originalHtml) {
            return true;
        }

        return false;
    }
}
