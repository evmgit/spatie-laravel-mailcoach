<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns;

use Spatie\Mailcoach\Domain\TransactionalMail\Support\TransactionalMailMessageConfig;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Header\AbstractHeader;

/** @mixin \Illuminate\Mail\Mailable */
trait StoresMail
{
    protected bool $store = false;

    public function store(): self
    {
        $this->store = true;

        $this->setMailCoachTrackingHeaders();

        return $this;
    }

    protected function setMailCoachTrackingHeaders(): self
    {
        $this->withSymfonyMessage(function (Email $message) {
            $this->removeExistingMailcoachHeaders($message);

            if ($this->store) {
                $this->addMailcoachHeader($message, 'X-MAILCOACH', 'true');
                $this->addMailcoachHeader($message, TransactionalMailMessageConfig::HEADER_NAME_STORE);
            }

            $this->addMailcoachHeader($message, TransactionalMailMessageConfig::HEADER_NAME_MAILABLE_CLASS, get_class($this));
        });

        return $this;
    }

    protected function setTransactionalHeader(): self
    {
        $this->withSymfonyMessage(function (Email $message) {
            $message->getHeaders()->remove(TransactionalMailMessageConfig::HEADER_NAME_TRANSACTIONAL);

            $this->addMailcoachHeader($message, TransactionalMailMessageConfig::HEADER_NAME_TRANSACTIONAL, true);
        });

        return $this;
    }

    protected function setMailableClassHeader(string $className): self
    {
        $this->withSymfonyMessage(function (Email $message) use ($className) {
            $message->getHeaders()->remove(TransactionalMailMessageConfig::HEADER_NAME_MAILABLE_CLASS);

            $this->addMailcoachHeader($message, TransactionalMailMessageConfig::HEADER_NAME_MAILABLE_CLASS, $className);
        });

        return $this;
    }

    protected function removeExistingMailcoachHeaders(Email $message): void
    {
        collect($message->getHeaders()->all())
            ->filter(function (AbstractHeader $header) {
                return in_array($header->getName(), TransactionalMailMessageConfig::getHeaderNames());
            })
            ->each(function (AbstractHeader $header) use ($message) {
                $message->getHeaders()->remove($header->getName());
            });
    }

    protected function addMailcoachHeader(
        Email $message,
        string $headerName,
        string $headerValue = ''
    ): self {
        $message
            ->getHeaders()
            ->addTextHeader(
                $headerName,
                $headerValue,
            );

        return $this;
    }
}
