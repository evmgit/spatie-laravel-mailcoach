<?php

namespace Spatie\Mailcoach\Domain\Shared\Support;

use InvalidArgumentException;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use RedisException;

class HorizonStatus
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_PAUSED = 'paused';

    public function __construct(
        private MasterSupervisorRepository $masterSupervisorRepository
    ) {
    }

    public function is(string $status): bool
    {
        return $this->get() === $status;
    }

    public function get(): string
    {
        try {
            $masters = $this->masterSupervisorRepository->all();
        } catch (RedisException|InvalidArgumentException $exception) {
            $masters = false;
        }

        if (! $masters) {
            return static::STATUS_INACTIVE;
        }

        $isPaused = collect($masters)->contains(fn ($master) => $master->status === 'paused');

        return $isPaused
            ? static::STATUS_PAUSED
            : static::STATUS_ACTIVE;
    }
}
