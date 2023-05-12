<?php

namespace Spatie\Mailcoach\Domain\Automation\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Actions\ShouldAutomationRunForSubscriberAction;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class RunAutomationForSubscriberJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UsesMailcoachModels;

    public $deleteWhenMissingModels = true;

    /** @var string */
    public $queue;

    /** @var ShouldAutomationRunForSubscriberAction */
    public $action;

    public function __construct(
        public Automation $automation,
        public Subscriber $subscriber,
    ) {
        $this->queue = config('mailcoach.automation.perform_on_queue.run_automation_for_subscriber_job');
        $this->action = resolve(config('mailcoach.automation.actions.should_run_for_subscriber', ShouldAutomationRunForSubscriberAction::class));

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {
        if ($this->automation->status !== AutomationStatus::STARTED) {
            return;
        }

        if (! $this->action->execute($this->automation, $this->subscriber)) {
            return;
        }

        $this->automation->run($this->subscriber);
    }

    public function retryUntil()
    {
        return now()->addHours(config('mailcoach.campaigns.throttling.retry_until_hours', 24));
    }
}
