<?php

namespace Spatie\Mailcoach\Domain\Audience\Events;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;

class TagAddedEvent
{
    public function __construct(
        public Subscriber $subscriber,
        public Tag $tag,
    ) {
    }
}
