<?php

namespace Spatie\Mailcoach\Domain\Shared\Events;

class ImportFinishedEvent
{
    /**
     * @param array{string: {finished: bool, progress: float, index: int, total: int, failed: bool, message: string}} $steps
     */
    public function __construct(public array $steps)
    {
    }
}
