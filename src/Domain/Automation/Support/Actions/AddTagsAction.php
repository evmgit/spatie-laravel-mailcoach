<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum;

class AddTagsAction extends AutomationAction
{
    public array $tags;

    public static function getCategory(): ActionCategoryEnum
    {
        return ActionCategoryEnum::Tags;
    }

    public static function make(array $data): self
    {
        return new self(explode(',', $data['tags'] ?? []));
    }

    public function __construct(array $tags)
    {
        parent::__construct();

        $this->tags = $tags;
    }

    public static function getName(): string
    {
        return (string) __mc('Add tags');
    }

    public static function getComponent(): ?string
    {
        return 'mailcoach::add-tags-action';
    }

    public function toArray(): array
    {
        return [
            'tags' => implode(',', $this->tags),
        ];
    }

    public function run(ActionSubscriber $actionSubscriber): void
    {
        $actionSubscriber->subscriber->addTags($this->tags);
    }
}
