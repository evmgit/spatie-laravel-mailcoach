<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Triggers;

use Spatie\Mailcoach\Domain\Audience\Events\TagAddedEvent;

class TagAddedTrigger extends AutomationTrigger implements TriggeredByEvents
{
    public string $tag = '';

    public function __construct(string $tag, ?string $uuid = null)
    {
        parent::__construct($uuid);

        $this->tag = $tag;
    }

    public static function getName(): string
    {
        return (string) __mc('When a tag gets added to a subscriber');
    }

    public static function getComponent(): ?string
    {
        return 'mailcoach::tag-added-trigger';
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
            TagAddedEvent::class,
            function (TagAddedEvent $event) {
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
