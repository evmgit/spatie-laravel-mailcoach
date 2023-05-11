<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum;

class SplitAction extends AutomationAction
{
    public function __construct(
        protected array $leftActions = [],
        protected array $rightActions = [],
        ?string $uuid = null,
    ) {
        parent::__construct($uuid);
    }

    public static function getCategory(): ActionCategoryEnum
    {
        return ActionCategoryEnum::Check;
    }

    public static function getName(): string
    {
        return (string) __mc('Branch out');
    }

    public static function getComponent(): ?string
    {
        return 'mailcoach::split-action';
    }

    public function duplicate(): static
    {
        $clone = parent::duplicate();

        $clone->leftActions = array_map(function (array $action) {
            $class = $action['class'];
            $action = $class::make($action['data']);

            return $action->duplicate();
        }, $clone->leftActions);

        $clone->rightActions = array_map(function (array $action) {
            $class = $action['class'];
            $action = $class::make($action['data']);

            return $action->duplicate();
        }, $clone->rightActions);

        return $clone;
    }

    public function store(string $uuid, Automation $automation, ?int $order = null, ?int $parent_id = null, ?string $key = null): Action
    {
        $parent = parent::store($uuid, $automation, $order, $parent_id, $key);

        $newChildrenUuids = collect($this->leftActions)->pluck('uuid')
            ->merge(collect($this->rightActions)->pluck('uuid'));

        $parent->children()->each(function (Action $existingChild) use ($newChildrenUuids) {
            if (! $newChildrenUuids->contains($existingChild->uuid)) {
                $existingChild->delete();
            }
        });

        foreach ($this->leftActions as $index => $action) {
            $this->storeChildAction(
                action: $action,
                automation: $automation,
                parent: $parent,
                key: 'leftActions',
                order: $index
            );
        }

        foreach ($this->rightActions as $index => $action) {
            $this->storeChildAction(
                action: $action,
                automation: $automation,
                parent: $parent,
                key: 'rightActions',
                order: $index
            );
        }

        return $parent;
    }

    protected function storeChildAction($action, Automation $automation, Action $parent, string $key, int $order): Action
    {
        if (! $action instanceof AutomationAction) {
            $uuid = $action['uuid'];
            $action = $action['class']::make($action['data']);
        }

        return $action->store(
            $uuid ?? Str::uuid()->toString(),
            $automation,
            $order,
            $parent->id,
            $key,
        );
    }

    public static function make(array $data): self
    {
        return new self(
            $data['leftActions'] ?? [],
            $data['rightActions'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'leftActions' => collect($this->leftActions)->map(function ($action) {
                return $this->actionToArray($action);
            })->toArray(),
            'rightActions' => collect($this->rightActions)->map(function ($action) {
                return $this->actionToArray($action);
            })->toArray(),
        ];
    }

    private function actionToArray(array|AutomationAction $action): array
    {
        $actionClass = static::getAutomationActionClass();
        $actionModel = $actionClass::query()
            ->where(
                'uuid',
                is_array($action)
                ? $action['uuid']
                : $action->uuid,
            )
            ->withCount(['completedSubscribers', 'activeSubscribers'])
            ->first();

        if (! $action instanceof AutomationAction) {
            $class = $action['class'];
            $action = $class::make($action['data']);
        }

        return [
            'uuid' => $action->uuid,
            'class' => $action::class,
            'data' => $action->toArray(),
            'active' => (int) ($actionModel->active_subscribers_count ?? 0),
            'completed' => (int) ($actionModel->completed_subscribers_count ?? 0),
        ];
    }

    public function nextActions(Subscriber $subscriber): array
    {
        $actionClass = static::getAutomationActionClass();
        $parentAction = $actionClass::findByUuid($this->uuid);

        $actions = [];
        if (isset($this->leftActions[0])) {
            $actions[] = $parentAction->children->where('key', 'leftActions')->first();
        }

        if (isset($this->rightActions[0])) {
            $actions[] = $parentAction->children->where('key', 'rightActions')->first();
        }

        if (count($actions)) {
            return $actions;
        }

        return parent::nextActions($subscriber);
    }
}
