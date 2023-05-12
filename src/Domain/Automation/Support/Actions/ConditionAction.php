<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Carbon\CarbonInterval;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum;

class ConditionAction extends AutomationAction
{
    public function __construct(
        protected CarbonInterval $checkFor,
        protected ?int $length = null,
        protected ?string $unit = null,
        protected array $yesActions = [],
        protected array $noActions = [],
        protected string $condition = '',
        protected array $conditionData = [],
        ?string $uuid = null,
    ) {
        parent::__construct($uuid);
    }

    public static function getCategory(): ActionCategoryEnum
    {
        return ActionCategoryEnum::check();
    }

    public static function getName(): string
    {
        return (string) __('If/Else');
    }

    public static function getComponent(): ?string
    {
        return 'condition-action';
    }

    public function duplicate(): static
    {
        $clone = parent::duplicate();

        $clone->yesActions = array_map(function (array $action) {
            $class = $action['class'];
            $action = $class::make($action['data']);

            return $action->duplicate();
        }, $clone->yesActions);

        $clone->noActions = array_map(function (array $action) {
            $class = $action['class'];
            $action = $class::make($action['data']);

            return $action->duplicate();
        }, $clone->noActions);

        return $clone;
    }

    public function store(string $uuid, Automation $automation, ?int $order = null, ?int $parent_id = null, ?string $key = null): Action
    {
        $parent = parent::store($uuid, $automation, $order, $parent_id, $key);

        $newChildrenUuids = collect($this->yesActions)->pluck('uuid')
            ->merge(collect($this->noActions)->pluck('uuid'));

        $parent->children()->each(function (Action $existingChild) use ($newChildrenUuids) {
            if (! $newChildrenUuids->contains($existingChild->uuid)) {
                $existingChild->delete();
            }
        });

        foreach (array_values($this->yesActions) as $index => $action) {
            $this->storeChildAction(
                action: $action,
                automation: $automation,
                parent: $parent,
                key: 'yesActions',
                order: $index
            );
        }

        foreach (array_values($this->noActions) as $index => $action) {
            $this->storeChildAction(
                action: $action,
                automation: $automation,
                parent: $parent,
                key: 'noActions',
                order: $index
            );
        }

        return $parent;
    }

    protected function storeChildAction($action, Automation $automation, Action $parent, string $key, int $order): Action
    {
        if (! $action instanceof AutomationAction) {
            /** @var \Spatie\Mailcoach\Domain\Automation\Support\Actions\AutomationAction $action */
            $uuid = $action['uuid'];
            $action = $action['class']::make($action['data']);
            $action->uuid = $uuid;
        }

        return $action->store(
            $action->uuid,
            $automation,
            $order,
            $parent->id,
            $key,
        );
    }

    public static function make(array $data): static
    {
        if (isset($data['seconds'])) {
            $interval = CarbonInterval::create(years: 0, seconds: $data['seconds']);
            $length = $data['length'] ?? null;
            $unit = $data['unit'] ?? null;
        } else {
            $interval = CarbonInterval::createFromDateString("{$data['length']} {$data['unit']}");
            $length = $data['length'] ?? null;
            $unit = $data['unit'] ?? null;
        }

        return new static(
            checkFor: $interval,
            length: $length,
            unit: $unit,
            yesActions: $data['yesActions'],
            noActions: $data['noActions'],
            condition: $data['condition'],
            conditionData: $data['conditionData'],
        );
    }

    public function toArray(): array
    {
        if (! isset($this->unit, $this->length)) {
            [$length, $unit] = explode(' ', $this->checkFor->forHumans());
            $this->length = (int) $length;
            $this->unit = $unit;
        }

        return [
            'seconds' => $this->checkFor->totalSeconds,
            'length' => $this->length,
            'unit' => $this->unit,
            'condition' => $this->condition,
            'conditionData' => $this->conditionData,
            'yesActions' => collect($this->yesActions)->map(function ($action) {
                return $this->actionToArray($action);
            })->toArray(),
            'noActions' => collect($this->noActions)->map(function ($action) {
                return $this->actionToArray($action);
            })->toArray(),
        ];
    }

    private function actionToArray(array | AutomationAction $action): array
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
            $uuid = $action['uuid'];
            $action = $class::make($action['data']);
            $action->uuid = $uuid;
        }

        return [
            'uuid' => $action->uuid,
            'class' => $action::class,
            'data' => $action->toArray(),
            'active' => (int) ($actionModel->active_subscribers_count ?? 0),
            'completed' => (int) ($actionModel->completed_subscribers_count ?? 0),
        ];
    }

    public function shouldContinue(Subscriber $subscriber): bool
    {
        $actionClass = static::getAutomationActionClass();
        $action = $actionClass::findByUuid($this->uuid);

        /** @var \Spatie\Mailcoach\Domain\Automation\Support\Conditions\Condition $condition */
        $conditionClass = $this->condition;
        $condition = new $conditionClass($action->automation, $subscriber, $this->conditionData);

        if ($condition->check()) {
            return true;
        }

        /** @var \Illuminate\Support\Carbon $addedToActionAt */
        $addedToActionAt = $subscriber->pivot->created_at;

        return $addedToActionAt->add($this->checkFor)->isPast();
    }

    public function nextActions(Subscriber $subscriber): array
    {
        $actionClass = static::getAutomationActionClass();

        $action = $actionClass::findByUuid($this->uuid);

        /** @var \Spatie\Mailcoach\Domain\Automation\Support\Conditions\Condition $condition */
        $conditionClass = $this->condition;
        $condition = new $conditionClass($action->automation, $subscriber, $this->conditionData);
        $nextAction = [];

        if ($condition->check()) {
            if (isset($this->yesActions[0])) {
                $nextAction = [$action->children->where('key', 'yesActions')->first()];
            }
        } else {
            if (isset($this->noActions[0])) {
                $nextAction = [$action->children->where('key', 'noActions')->first()];
            }
        }

        return $nextAction ?: $this->getNextActionNested($action);
    }
}
