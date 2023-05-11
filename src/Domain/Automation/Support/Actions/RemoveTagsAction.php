<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum;

class RemoveTagsAction extends AutomationAction
{
    public array $tags;

    public static function getCategory(): ActionCategoryEnum
    {
        return ActionCategoryEnum::Tags;
    }

    public static function make(array $data): self
    {
        return new self(explode(',', $data['tags'] ?? ''));
    }

    public function __construct(array $tags)
    {
        parent::__construct();

        $this->tags = $tags;
    }

    public static function getName(): string
    {
        return (string) __mc('Remove tags');
    }

    public function getDescription(): string
    {
        return implode(', ', $this->tags);
    }

    public static function getComponent(): ?string
    {
        return 'mailcoach::remove-tags-action';
    }

    public function toArray(): array
    {
        return [
            'tags' => implode(',', $this->tags),
        ];
    }

    public function run(ActionSubscriber $actionSubscriber): void
    {
        $actionSubscriber->subscriber->removeTags($this->tags);
    }
}
