<?php

namespace Spatie\Mailcoach\Domain\Automation\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class RunAutomationActionsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UsesMailcoachModels;

    public int $uniqueFor = 60;

    public function __construct()
    {
        $this->onQueue(config('mailcoach.shared.perform_on_queue.schedule'));
    }

    public function handle()
    {
        static::getAutomationClass()::query()
            ->where('status', AutomationStatus::Started)
            ->lazyById()
            ->each(function (Automation $automation) {
                if (! is_null($automation->run_at) && $automation->run_at->add($automation->interval ?? '10 minutes')->isFuture()) {
                    return;
                }

                info("Dispatching jobs for all actions for automation id `{$automation->id}`");

                $automation->allActions()->each(function (Action $action) {
                    return dispatch(new RunAutomationActionJob($action));
                });

                $automation->update(['run_at' => now()]);
            });
    }
}
