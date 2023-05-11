<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Triggers;

use Spatie\Mailcoach\Domain\Audience\Events\TagRemovedEvent;

class TagRemovedTrigger extends AutomationTrigger implements TriggeredByEvents
{
    public string $tag = '';

    public function __construct(string $tag, ?string $uuid = null)
    {
        parent::__construct($uuid);

        $this->tag = $tag;
    }

    public static function getName(): string
    {
        return (string) __mc('When a tag gets removed from a subscriber');
    }

    public static function getComponent(): ?string
    {
        return 'mailcoach::tag-removed-trigger';
    }

    public static function rules(): array
    {
        return [
            'tag' => ['required'],
        ];
    }

    public function subscribe($events): void
    {
        $events->listen(
            TagRemovedEvent::class,
            function ($event) {
                if ($event->tag->name === $this->tag) {
                    $this->runAutomation($event->subscriber);
                }
            }
        );
    }

    public static function make(array $data): self
    {
        return new self($data['tag']);
    }
}
