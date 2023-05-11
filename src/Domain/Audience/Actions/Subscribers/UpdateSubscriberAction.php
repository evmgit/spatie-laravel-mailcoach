<?php

namespace Spatie\Mailcoach\Domain\Audience\Actions\Subscribers;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

class UpdateSubscriberAction
{
    public bool $replaceTags = true;

    public function execute(Subscriber $subscriber, array $attributes, array $tags = []): void
    {
        $subscriber->fill($attributes);

        if ($this->replaceTags) {
            $subscriber->syncTags($tags);
        } else {
            $subscriber->addTags($tags);
        }

        $subscriber->save();
    }

    public function appendTags(): self
    {
        $this->replaceTags = false;

        return $this;
    }
}
