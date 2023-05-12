<?php

namespace Spatie\Mailcoach\Domain\Shared\Support;

use Illuminate\Support\Facades\Cache;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;

class CalculateStatisticsLock
{
    private string $key;

    private int $lockTimeInSeconds;

    public function __construct(Sendable $sendable, int $lockTimeInSeconds = 10)
    {
        $this->key = "calculate-statistics-lock-{$sendable->uuid}";

        $this->lockTimeInSeconds = $lockTimeInSeconds;
    }

    public function get(): bool
    {
        $cachedValue = Cache::get($this->key);

        if (is_null($cachedValue)) {
            $this->setLock();

            return true;
        }

        if (now()->timestamp >= $cachedValue) {
            $this->setLock();

            return true;
        }

        return false;
    }

    public function release(): void
    {
        Cache::set($this->key, 0);
    }

    protected function setLock(): void
    {
        Cache::set($this->key, now()->addSeconds($this->lockTimeInSeconds)->timestamp);
    }
}
