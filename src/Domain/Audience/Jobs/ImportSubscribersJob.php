<?php

namespace Spatie\Mailcoach\Domain\Audience\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ImportSubscribersAction;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Mailcoach;

class ImportSubscribersJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public SubscriberImport $subscriberImport;

    public ?User $user;

    public function __construct(SubscriberImport $subscriberImport, User $user = null)
    {
        $this->subscriberImport = $subscriberImport;

        $this->user = $user;

        $this->queue = config('mailcoach.campaigns.perform_on_queue.import_subscribers_job');

        $this->connection = $this->connection ?? Mailcoach::getQueueConnection();
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ImportSubscribersAction $importSubscribersAction */
        $importSubscribersAction = Mailcoach::getAudienceActionClass('import_subscribers', ImportSubscribersAction::class);
        $importSubscribersAction->execute($this->subscriberImport, $this->user);
    }
}
