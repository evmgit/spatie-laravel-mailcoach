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
use Spatie\Mailcoach\Domain\Automation\Support\Actions\AutomationAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\AutomationTrigger;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasUuid;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\SendsToSegment;
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

    ];

    public static function booted()
    {
        static::creating(function (Automation $automation) {
            if (! $automation->status) {
                $automation->status = AutomationStatus::PAUSED;
            }
        });

        static::saved(function () {
            cache()->forget('automation-triggers');
        });
    }

    public function name(string $name): self
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
        return $this->triggers->first()?->trigger;
    }

    public function triggerClass(): string
    {
        if ($trigger = $this->triggers->first()) {
            return $trigger->getAutomationTrigger()::class;
        }

        return '';
    }

    public function triggerOn(AutomationTrigger $automationTrigger): self
    {
        $trigger = $this->triggers()->firstOrCreate([]);
        $trigger->trigger = $automationTrigger;
        $trigger->save();

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

    public function to(EmailList $emailList): self
    {
        $this->update(['email_list_id' => $emailList->id]);

        return $this;
    }

    public function runEvery(CarbonInterval $interval): self
    {
        $this->update(['interval' => $interval]);

        return $this;
    }

    public function chain(array $chain): self
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

        if ($this->status === AutomationStatus::STARTED && $this->actions->count() === 0) {
            $this->pause();
        }

        return $this;
    }

    public function pause(): self
    {
        $this->update(['status' => AutomationStatus::PAUSED]);

        return $this;
    }

    public function start(): self
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

        if ($this->status === AutomationStatus::STARTED) {
            throw CouldNotStartAutomation::started($this);
        }

        $this->update(['status' => AutomationStatus::STARTED]);

        return $this;
    }

    public function run(Subscriber $subscriber): void
    {
        $this->actions()->first()->subscribers()->attach($subscriber);

        $this->update(['last_ran_at' => now()]);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        return static::getAutomationClass()::where($field, $value)->firstOrFail();
    }

    protected static function newFactory(): AutomationFactory
    {
        return new AutomationFactory();
    }
}
