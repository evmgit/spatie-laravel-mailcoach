<?php

namespace Spatie\Mailcoach\Domain\Audience\Support\Segments;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

abstract class Segment
{
    protected Model $segmentable;

    public function setSegmentable(Model $segmentable): self
    {
        $this->segmentable = $segmentable;

        return $this;
    }

    abstract public function description(): string;

    public function subscribersQuery(Builder $subscribersQuery): void
    {
    }

    public function shouldSend(Subscriber $subscriber): bool
    {
        return true;
    }
}
