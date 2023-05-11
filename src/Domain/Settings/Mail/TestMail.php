<?php

namespace Spatie\Mailcoach\Domain\Settings\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $theme = 'mailcoach::mails.layout.mailcoach';

    private string $fromEmail;

    private string $toEmail;

    public function __construct(string $fromEmail, string $toEmail)
    {
        $this->fromEmail = $fromEmail;
        $this->toEmail = $toEmail;
    }

    public function build()
    {
        return $this
            ->subject(__mc('Mailcoach testmail'))
            ->to($this->toEmail)
            ->from($this->fromEmail)
            ->markdown('mailcoach::mails.test');
    }
}
