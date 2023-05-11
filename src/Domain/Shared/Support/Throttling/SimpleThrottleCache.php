<?php

namespace Spatie\Mailcoach\Domain\Shared\Support\Throttling;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Cache\Repository;

class SimpleThrottleCache
{
    protected string $currentPeriodHitCountKey = 'simpleThrottle.currentPeriodHitCount';

    protected string $currentPeriodEndsAtKey = 'simpleThrottle.currentPeriodEndsAt';

    public function __construct(protected Repository $cache)
    {
    }

    public function forMailer(?string $mailer = null): self
    {
        if (! is_null($mailer)) {
            $this->currentPeriodHitCountKey = "simpleThrottle.currentPeriodHitCount.{$mailer}";
            $this->currentPeriodEndsAtKey = "simpleThrottle.currentPeriodEndsAt.{$mailer}";
        }

        return $this;
    }

    public function forMailerCreates(?string $mailer = null): self
    {
        if (! is_null($mailer)) {
            $this->currentPeriodHitCountKey = "simpleThrottle.currentPeriodHitCount.createSends.{$mailer}";
            $this->currentPeriodEndsAtKey = "simpleThrottle.currentPeriodEndsAt.createSends.{$mailer}";
        }

        return $this;
    }

    public function currentPeriodHitCount(): int
    {
        return $this->cache->get($this->currentPeriodHitCountKey) ?? 0;
    }

    public function setCurrentPeriodHitCount(int $hitCount): int
    {
        return $this->cache->set($this->currentPeriodHitCountKey, $hitCount);
    }

    public function increaseCurrentPeriodHitCount(): self
    {
        $this->cache->increment($this->currentPeriodHitCountKey);

        return $this;
    }

    public function periodEndsAt(): ?Carbon
    {
        $timestamp = $this->cache->get($this->currentPeriodEndsAtKey);

        if (! $timestamp) {
            return null;
        }

        return Carbon::createFromTimestamp($timestamp);
    }

    public function setPeriodEndsAt(CarbonInterface $endsAt): self
    {
        $timestamp = $endsAt->timestamp;

        $this->cache->set($this->currentPeriodEndsAtKey, $timestamp);

        return $this;
    }
}
