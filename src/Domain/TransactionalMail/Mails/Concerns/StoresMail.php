<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns;

use Spatie\Mailcoach\Domain\TransactionalMail\Support\TransactionalMailMessageConfig;
use Swift_Message;
use Swift_Mime_Headers_AbstractHeader;

/** @mixin \Illuminate\Mail\Mailable */
trait StoresMail
{
    protected bool $trackOpens = false;

    protected bool $trackClicks = false;

    protected bool $store = false;

    public function trackOpensAndClicks(): self
    {
        $this->store = true;
        $this->trackOpens = true;
        $this->trackClicks = true;

        $this->setMailCoachTrackingHeaders();

        return $this;
    }

    public function trackOpens(): self
    {
        $this->store = true;
        $this->trackOpens = true;

        $this->setMailCoachTrackingHeaders();

        return $this;
    }

    public function trackClicks(): self
    {
        $this->store = true;
        $this->trackClicks = true;

        $this->setMailCoachTrackingHeaders();

        return $this;
    }

    public function store(): self
    {
        $this->store = true;

        $this->setMailCoachTrackingHeaders();

        return $this;
    }

    protected function setMailCoachTrackingHeaders(): self
    {
        $this->withSwiftMessage(function (Swift_Message $message) {
            $this->removeExistingMailcoachHeaders($message);

            if ($this->trackOpens) {
                $this->addMailcoachHeader($message, TransactionalMailMessageConfig::HEADER_NAME_OPENS);
            }

            if ($this->trackClicks) {
                $this->addMailcoachHeader($message, TransactionalMailMessageConfig::HEADER_NAME_CLICKS);
            }

            if ($this->trackOpens || $this->trackClicks) {
                $this->addMailcoachHeader($message, 'X-MAILCOACH', 'true');
            }

            if ($this->store) {
                $this->addMailcoachHeader($message, TransactionalMailMessageConfig::HEADER_NAME_STORE);
            }

            $this->addMailcoachHeader($message, TransactionalMailMessageConfig::HEADER_NAME_MAILABLE_CLASS, get_class($this));
        });

        return $this;
    }

    protected function setMailableClassHeader(string $className): self
    {
        $this->withSwiftMessage(function (Swift_Message $message) use ($className) {
            $message->getHeaders()->removeAll(TransactionalMailMessageConfig::HEADER_NAME_MAILABLE_CLASS);

            $this->addMailcoachHeader($message, TransactionalMailMessageConfig::HEADER_NAME_MAILABLE_CLASS, $className);
        });

        return $this;
    }

    protected function removeExistingMailcoachHeaders(Swift_Message $message): void
    {
        collect($message->getHeaders()->getAll())
            ->filter(function (Swift_Mime_Headers_AbstractHeader $header) {
                return in_array($header->getFieldName(), TransactionalMailMessageConfig::getHeaderNames());
            })
            ->each(function (Swift_Mime_Headers_AbstractHeader $header) use ($message) {
                $message->getHeaders()->removeAll($header->getFieldName());
            });
    }

    protected function addMailcoachHeader(
        Swift_Message $message,
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
