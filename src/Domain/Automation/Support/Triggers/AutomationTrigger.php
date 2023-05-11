<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Triggers;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunAutomationForSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\AutomationStep;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

abstract class AutomationTrigger extends AutomationStep
{
    use UsesMailcoachModels;

    protected Automation $automation;

    public function setAutomation(Automation $automation): static
    {
        $this->automation = $automation;

        return $this;
    }

    public static function rules(): array
    {
        return [];
    }

    public function runAutomation(Subscriber|Collection|QueryBuilder|EloquentBuilder|array $subscribers): void
    {
        if ($subscribers instanceof EloquentBuilder || $subscribers instanceof QueryBuilder) {
            $subscribers = $subscribers->lazyById();
        }

        if ($subscribers instanceof Subscriber || is_array($subscribers)) {
            $subscribers = collect(Arr::wrap($subscribers))->lazy();
        }

        if ($subscribers instanceof Collection) {
            $subscribers = $subscribers->lazy();
        }

        $subscribers
            ->each(function (Subscriber $subscriber) {
                dispatch(new RunAutomationForSubscriberJob($this->automation, $subscriber));
            });
    }
}
