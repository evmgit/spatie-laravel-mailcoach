<?php

namespace Spatie\Mailcoach\Domain\Settings\Enums;

enum MailerTransport: string
{
    case Mailgun = 'mailgun';
    case Postmark = 'postmark';
    case SendGrid = 'sendGrid';
    case Sendinblue = 'sendinblue';
    case Ses = 'ses';
    case Smtp = 'smtp';

    public function label(): string
    {
        return match ($this) {
            self::Ses => 'Amazon SES',
            self::SendGrid => 'SendGrid',
            self::Smtp => 'SMTP',
            self::Postmark => 'Postmark',
            self::Mailgun => 'Mailgun',
            self::Sendinblue => 'Sendinblue',
        };
    }
}
