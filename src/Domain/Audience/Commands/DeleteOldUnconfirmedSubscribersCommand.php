<?php

namespace Spatie\Mailcoach\Domain\Audience\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\DeleteSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class DeleteOldUnconfirmedSubscribersCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:delete-old-unconfirmed-subscribers';

    public $description = 'Delete all unsubscribed subscribers';

    public function handle()
    {
        $this->comment('Deleting old unconfirmed subscribers...');

        $cutOffDate = now()->subMonth()->toDateTimeString();

        /** @var DeleteSubscriberAction $deleteSubscriberAction */
        $deleteSubscriberAction = Config::getAudienceActionClass('delete_subscriber', DeleteSubscriberAction::class);

        $deletedSubscribersCount = $this->getSubscriberClass()::unconfirmed()
            ->where('created_at', '<', $cutOffDate)
            ->each(function (Subscriber $subscriber) use ($deleteSubscriberAction) {
                $deleteSubscriberAction->execute($subscriber);
            });

        $this->comment("Deleted {$deletedSubscribersCount} unconfirmed subscribers.");
    }
}
