<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Support;

use Symfony\Component\Mime\Email;

class TransactionalMailMessageConfig
{
    public const HEADER_NAME_TRANSACTIONAL = 'mailcoach-transactional-mail';

    public const HEADER_NAME_OPENS = 'mailcoach-transactional-mail-config-track-opens';

    public const HEADER_NAME_CLICKS = 'mailcoach-transactional-mail-config-track-clicks';

    public const HEADER_NAME_STORE = 'mailcoach-transactional-mail-config-store';

    public const HEADER_NAME_MAILABLE_CLASS = 'mailcoach-transactional-mail-config-mailable-class';

    public static function createForMessage(Email $message): self
    {
        return new self($message);
    }

    protected function __construct(
        protected Email $message
    ) {
    }

    public function isTransactionalMail(): bool
    {
        return $this->message->getHeaders()->has(static::HEADER_NAME_TRANSACTIONAL);
    }

    public function shouldStore(): bool
    {
        return $this->message->getHeaders()->has(static::HEADER_NAME_STORE);
    }

    public function getMailableClass(): string
    {
        return $this->message->getHeaders()->get(static::HEADER_NAME_MAILABLE_CLASS)->getBodyAsString();
    }

    public static function getHeaderNames(): array
    {
        return [
            static::HEADER_NAME_OPENS,
            static::HEADER_NAME_CLICKS,
            static::HEADER_NAME_STORE,
            static::HEADER_NAME_MAILABLE_CLASS,
        ];
    }
}
