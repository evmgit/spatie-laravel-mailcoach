<?php

namespace Spatie\Mailcoach\Domain\Automation\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CalculateAutomationMailStatisticsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UsesMailcoachModels;

    public int $uniqueFor = 60;

    public function __construct(protected ?int $automationMailId = null)
    {
        $this->onQueue(config('mailcoach.shared.perform_on_queue.schedule'));
    }

    public function handle()
    {
        Cache::put('mailcoach-last-schedule-run', now());

        $this->automationMailId
            ? CalculateStatisticsJob::dispatchSync(self::getAutomationMailClass()::find($this->automationMailId))
            : $this->calculateStatisticsOfAutomationMails();
    }

    protected function calculateStatisticsOfAutomationMails(): void
    {
        $this->now = now();

        static::getAutomationClass()::query()
            ->where('status', AutomationStatus::Started)
            ->with(['allActions'])
            ->get()
            ->flatMap(function (Automation $automation) {
                return $automation->allActions;
            })->filter(function (Action $action) {
                return $action->action::class === SendAutomationMailAction::class;
            })->map(function (Action $action) {
                return $action->action->automationMail;
            })->each(function (AutomationMail $automationMail) {
                $automationMail->dispatchCalculateStatistics();
            });
    }
}
