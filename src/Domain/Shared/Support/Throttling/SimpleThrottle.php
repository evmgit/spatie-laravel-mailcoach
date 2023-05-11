<?php

namespace Spatie\Mailcoach\Domain\Shared\Support\Throttling;

class SimpleThrottle
{
    public static function create(SimpleThrottleCache $cache)
    {
        return new static($cache);
    }

    public function __construct(
        protected SimpleThrottleCache $cache,
        protected int $allowedNumberInPeriod = 10,
        protected int $periodLengthInSeconds = 1,
    ) {
    }

    public function forMailer(?string $mailer = null): self
    {
        $this->cache->forMailer($mailer);

        $this->allowedNumberInPeriod = config("mail.mailers.{$mailer}.mails_per_timespan", $this->allowedNumberInPeriod);
        $this->periodLengthInSeconds = config("mail.mailers.{$mailer}.timespan_in_seconds", $this->periodLengthInSeconds);

        return $this;
    }

    public function forMailerCreates(?string $mailer = null): self
    {
        $this->cache->forMailerCreates($mailer);

        $mailsPerSecond = config("mail.mailers.{$mailer}.mails_per_timespan", $this->allowedNumberInPeriod) / config("mail.mailers.{$mailer}.timespan_in_seconds", $this->periodLengthInSeconds);

        $this->allowedNumberInPeriod = ceil($mailsPerSecond * 2);
        $this->periodLengthInSeconds = 1;

        return $this;
    }

    public function allow(int $allowedInPeriod): self
    {
        $this->allowedNumberInPeriod = $allowedInPeriod;

        return $this;
    }

    public function inSeconds(int $timespanInSeconds): self
    {
        $this->periodLengthInSeconds = $timespanInSeconds;

        return $this;
    }

    public function hit(): self
    {
        if (is_null($this->cache->periodEndsAt())) {
            $this->resetPeriod();
        }

        if ($this->cache->currentPeriodHitCount() >= $this->allowedNumberInPeriod) {
            $this
                ->sleepUntilEndOfTimeSpan()
                ->resetPeriod();
        }

        $this->cache->increaseCurrentPeriodHitCount();

        return $this;
    }

    protected function sleepUntilEndOfTimeSpan(): self
    {
        if (! $this->cache->periodEndsAt()->isFuture()) {
            return $this;
        }

        $sleepSeconds = $this->cache->periodEndsAt()->diffInSeconds() + 1;

        sleep($sleepSeconds);

        return $this;
    }

    protected function resetPeriod(): self
    {
        $periodEndsAt = now()->addSeconds($this->periodLengthInSeconds);

        $this->cache->setPeriodEndsAt($periodEndsAt);
        $this->cache->setCurrentPeriodHitCount(0);

        return $this;
    }
}
