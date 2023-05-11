<?php

namespace Spatie\Mailcoach\Domain\Automation\Models;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Mailcoach\Database\Factories\AutomationFactory;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Exceptions\CouldNotStartAutomation;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunActionForActionSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\AutomationAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\AutomationTrigger;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\SendsToSegment;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class Automation extends Model
{
    use HasUuid;
    use UsesMailcoachModels;
    use SendsToSegment;
    use HasFactory;

    public $table = 'mailcoach_automations';

    protected $guarded = [];

    protected $casts = [
        'run_at' => 'datetime',
        'last_ran_at' => 'datetime',
        'status' => AutomationStatus::class,
        'repeat_enabled' => 'boolean',
        'repeat_only_after_halt' => 'boolean',
    ];

    public static function booted()
    {
        static::creating(function (Automation $automation) {
            if (! $automation->status) {
                $automation->status = AutomationStatus::Paused;
            }
        });

        static::saved(function () {
            cache()->forget('automation-triggers');
        });
    }

    public function name(string $name): static
    {
        $this->update(compact('name'));

        return $this;
    }

    public function triggers(): HasMany
    {
        return $this->hasMany(static::getAutomationTriggerClass());
    }

    public function getTrigger(): ?AutomationTrigger
    {
        /** @var ?\Spatie\Mailcoach\Domain\Automation\Models\Trigger $trigger */
        $trigger = $this->triggers->first();

        $automation = clone $this;
        $automation->unsetRelation('triggers');

        $trigger?->setRelation('automation', $automation);

        return $trigger?->trigger;
    }

    public function triggerClass(): string
    {
        if ($trigger = $this->triggers->first()) {
            return $trigger->getAutomationTrigger()::class;
        }

        return '';
    }

    public function triggerOn(AutomationTrigger $automationTrigger): static
    {
        $trigger = $this->triggers()->firstOrCreate([]);
        $trigger->trigger = $automationTrigger;
        $trigger->save();

        return $this;
    }

    public function repeat(bool $repeatEnabled = true, bool $onlyAfterHalt = true): static
    {
        $this->repeat_enabled = $repeatEnabled;
        $this->repeat_only_after_halt = $onlyAfterHalt;
        $this->save();

        return $this;
    }

    public function actions(): HasMany
    {
        return $this->hasMany(static::getAutomationActionClass())->whereNull('parent_id')->orderBy('order');
    }

    public function allActions(): HasMany
    {
        return $this->hasMany(static::getAutomationActionClass())->orderBy('order');
    }

    public function emailList(): BelongsTo
    {
        return $this->belongsTo($this->getEmailListClass());
    }

    public function newSubscribersQuery(): Builder
    {
        $subscribersQuery = $this->baseSubscribersQuery();
        $segment = $this->getSegment();
        $segment->subscribersQuery($subscribersQuery);

        return $subscribersQuery;
    }

    public function to(EmailList $emailList): static
    {
        $this->update(['email_list_id' => $emailList->id]);

        return $this;
    }

    public function runEvery(CarbonInterval $interval): static
    {
        $this->update(['interval' => $interval]);

        return $this;
    }

    public function chain(array $chain): static
    {
        $newActions = collect($chain);

        $this->actions()->each(function ($existingAction) use ($newActions) {
            if (! $newActions->pluck('uuid')->contains($existingAction->uuid)) {
                $existingAction->delete();
            }
        });

        $newActions->each(function ($action, $index) {
            if (! $action instanceof AutomationAction) {
                $uuid = $action['uuid'];
                /** @var \Spatie\Mailcoach\Domain\Automation\Support\Actions\AutomationAction $action */
                $action = $action['class']::make($action['data']);
                $action->uuid = $uuid;
            }

            $action->store($action->uuid, $this, $index);
        });

        $this->fresh('actions');

        if ($this->status === AutomationStatus::Started && $this->actions->count() === 0) {
            $this->pause();
        }

        return $this;
    }

    public function pause(): static
    {
        $this->update(['status' => AutomationStatus::Paused]);

        return $this;
    }

    public function start(): static
    {
        if (! $this->interval) {
            throw CouldNotStartAutomation::noInterval($this);
        }

        if (! $this->emailList()->count() > 0) {
            throw CouldNotStartAutomation::noListSet($this);
        }

        if (! $this->triggers()->count() > 0) {
            throw CouldNotStartAutomation::noTrigger($this);
        }

        if (! $this->actions()->count() > 0) {
            throw CouldNotStartAutomation::noActions($this);
        }

        if ($this->status === AutomationStatus::Started) {
            throw CouldNotStartAutomation::started($this);
        }

        $this->update(['status' => AutomationStatus::Started]);

        return $this;
    }

    public function run(Subscriber $subscriber): void
    {
        /** @var null|Action $firstAction */
        $firstAction = $this
            ->actions()
            ->first();

        if (! $firstAction) {
            return;
        }

        $actionSubscriber = self::getActionSubscriberClass()::create([
            'job_dispatched_at' => now(),
            'action_id' => $firstAction->id,
            'subscriber_id' => $subscriber->id,
        ]);

        $this->update(['last_ran_at' => now()]);

        dispatch(new RunActionForActionSubscriberJob($actionSubscriber));
    }

    protected static function newFactory(): AutomationFactory
    {
        return new AutomationFactory();
    }
}
