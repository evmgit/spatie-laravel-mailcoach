<?php

namespace Spatie\Mailcoach\Domain\Automation\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\AutomationAction;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class RunActionForSubscriberJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UsesMailcoachModels;

    public Action $action;

    public Subscriber $subscriber;

    /** @var string */
    public $queue;

    public function __construct(Action $action, Subscriber $subscriber)
    {
        $this->action = $action;

        $this->subscriber = $subscriber;

        $this->queue = config('mailcoach.automation.perform_on_queue.run_action_for_subscriber_job');

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {
        /** @var AutomationAction $action */
        $action = $this->action->action;

        $actionSubscribers = self::getActionSubscriberClass()::query()
            ->where('subscriber_id', '=', $this->subscriber->id)
            ->where('action_id', '=', $this->action->id)
            ->whereNull(['halted_at', 'completed_at'])
            ->with(['subscriber'])
            ->get();

        if (! $actionSubscribers->count()) {
            return;
        }

        $actionSubscribers->each(function (ActionSubscriber $actionSubscriber) use ($action) {
            /** @var Subscriber $subscriber */
            $subscriber = $actionSubscriber->subscriber;
            $subscriber->setRelation('pivot', $actionSubscriber);

            if (is_null($actionSubscriber->run_at)) {
                /** @psalm-suppress TooManyArguments */
                $action->run($subscriber, $actionSubscriber);

                if ($action->shouldHalt($subscriber) || ! $subscriber->isSubscribed()) {
                    $actionSubscriber->update([
                        'halted_at' => now(),
                        'run_at' => now(),
                    ]);

                    return;
                }

                if (! $action->shouldContinue($subscriber)) {
                    return;
                }

                $actionSubscriber->update(['run_at' => now()]);
            }

            if (is_null($actionSubscriber->completed_at)) {
                $nextActions = $action->nextActions($subscriber);

                if (count(array_filter($nextActions))) {
                    foreach ($nextActions as $nextAction) {
                        $nextAction->attachSubscriber($subscriber, $actionSubscriber);
                    }

                    $actionSubscriber->update(['completed_at' => now()]);
                }
            }
        });
    }
}
