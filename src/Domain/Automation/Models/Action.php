<?php

namespace Spatie\Mailcoach\Domain\Automation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Mailcoach\Database\Factories\ActionFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunActionForActionSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\AutomationAction;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class Action extends Model
{
    use HasUuid;
    use HasFactory;
    use UsesMailcoachModels;

    public $table = 'mailcoach_automation_actions';

    protected $guarded = [];

    protected $casts = [
        'order' => 'int',
    ];

    public function setActionAttribute(AutomationAction $value)
    {
        $this->attributes['action'] = base64_encode(serialize($value));
    }

    public function getActionAttribute(string $value): AutomationAction
    {
        /** @var AutomationAction $action */
        if (base64_encode(base64_decode($value, true)) === $value) {
            $action = unserialize(base64_decode($value));
        } else {
            $action = unserialize($value);
        }

        $action->uuid = $this->uuid;

        return $action;
    }

    /**
     * We have this method which accepts the previous pivot as a way to override the Action
     * model and add custom logic to the automation actions process. For example to add
     * some extra context on the pivot model and pass that context along to the next.
     */
    public function attachSubscriber(Subscriber $subscriber, ActionSubscriber $previousPivot): void
    {
        $this->subscribers()->attach($subscriber);
    }

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(static::getSubscriberClass(), static::getActionSubscriberTableName())
            ->withPivot(['completed_at', 'halted_at', 'run_at'])
            ->using($this->getActionSubscriberClass())
            ->withTimestamps();
    }

    public function actionSubscribers(): HasMany
    {
        return $this->hasMany(self::getActionSubscriberClass());
    }

    public function pendingActionSubscribers(): HasMany
    {
        return $this
            ->actionSubscribers()
            ->whereNull(['halted_at', 'completed_at', 'job_dispatched_at']);
    }

    public function activeSubscribers(): BelongsToMany
    {
        return $this->subscribers()
            ->wherePivotNull('halted_at')
            ->wherePivotNull('run_at');
    }

    public function completedSubscribers(): BelongsToMany
    {
        return $this->subscribers()
            ->wherePivotNotNull('run_at');
    }

    public function haltedSubscribers(): BelongsToMany
    {
        return $this->subscribers()
            ->wherePivotNotNull('halted_at');
    }

    public function automation(): BelongsTo
    {
        return $this->belongsTo(static::getAutomationClass());
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::getAutomationActionClass(), 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(static::getAutomationActionClass(), 'parent_id')->orderBy('order');
    }

    public function toLivewireArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'class' => get_class($this->action),
            'data' => $this->action->toArray(),
            'active' => (int) ($this->active_subscribers_count ?? 0),
            'completed' => ($this->completed_subscribers_count ?? 0) - ($this->halted_subscribers_count ?? 0),
            'halted' => (int) ($this->halted_subscribers_count ?? 0),
        ];
    }

    public function run()
    {
        $this->action->getActionSubscribersQuery($this)
            ->lazyById()
            ->each(function (ActionSubscriber $actionSubscriber): void {
                $actionSubscriber->update(['job_dispatched_at' => now()]);

                dispatch(new RunActionForActionSubscriberJob($actionSubscriber))->afterCommit();
            });
    }

    protected static function newFactory(): ActionFactory
    {
        return new ActionFactory();
    }
}
