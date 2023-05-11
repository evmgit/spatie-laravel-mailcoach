<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum;
use Spatie\Mailcoach\Domain\Automation\Support\AutomationStep;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

abstract class AutomationAction extends AutomationStep
{
    use UsesMailcoachModels;

    abstract public static function getCategory(): ActionCategoryEnum;

    public function run(ActionSubscriber $actionSubscriber): void
    {
    }

    public function shouldContinue(ActionSubscriber $actionSubscriber): bool
    {
        return true;
    }

    public function shouldHalt(ActionSubscriber $actionSubscriber): bool
    {
        return false;
    }

    public function duplicate(): static
    {
        $clone = clone $this;
        $clone->uuid = Str::uuid()->toString();

        return $clone;
    }

    public function getActionSubscribersQuery(Action $action): Builder|\Illuminate\Database\Eloquent\Builder|Relation
    {
        return $action->pendingActionSubscribers();
    }

    public function store(string $uuid, Automation $automation, ?int $order = null, ?int $parent_id = null, ?string $key = null): Action
    {
        $actionClass = static::getAutomationActionClass();

        return $actionClass::updateOrCreate([
            'uuid' => $uuid,
        ], [
            'automation_id' => $automation->id,
            'order' => $order ?? $automation->actions()->max('order') + 1,
            'action' => $this,
            'parent_id' => $parent_id,
            'key' => $key,
        ]);
    }

    /** @return Action[] */
    public function nextActions(Subscriber $subscriber): array
    {
        $actionClass = static::getAutomationActionClass();
        $action = $actionClass::findByUuid($this->uuid);

        return $this->nextActionsForAction($action);
    }

    public function nextActionsForAction(Action $action): array
    {
        if ($action->children->count()) {
            return [$action->children->first()];
        }

        return $this->getNextActionNested($action);
    }

    public function getNextActionNested(Action $action): array
    {
        if (! $action->parent_id) {
            return [$action->automation->actions->where('order', '>', $action->order)->first()];
        }

        if ($action->key && $nextAction = $action->parent->children->where('key', $action->key)->where('order', '>', $action->order)->first()) {
            return [$nextAction];
        }

        if ($nextAction = $action->parent->children->where('order', '>', $action->order)->first()) {
            return [$nextAction];
        }

        return $this->getNextActionNested($action->parent);
    }
}
