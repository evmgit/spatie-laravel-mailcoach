<?php

namespace Spatie\Mailcoach\Domain\Audience\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\DeleteSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class DeleteOldUnconfirmedSubscribersJob implements ShouldQueue, ShouldBeUnique
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
        info('Deleting old unconfirmed subscribers...');

        $cutOffDate = now()->subMonth()->toDateTimeString();

        /** @var DeleteSubscriberAction $deleteSubscriberAction */
        $deleteSubscriberAction = Mailcoach::getAudienceActionClass('delete_subscriber', DeleteSubscriberAction::class);

        self::getSubscriberClass()::unconfirmed()
            ->where('created_at', '<', $cutOffDate)
            ->each(function (Subscriber $subscriber) use ($deleteSubscriberAction) {
                $deleteSubscriberAction->execute($subscriber);
            });
    }
}
