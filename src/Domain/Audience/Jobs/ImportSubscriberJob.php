<?php

namespace Spatie\Mailcoach\Domain\Audience\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ImportSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Domain\Audience\Support\ImportSubscriberRow;
use Spatie\Mailcoach\Mailcoach;

class ImportSubscriberJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public SubscriberImport $subscriberImport;

    public array $values;

    public $maxAttempts = 3;

    public function __construct(SubscriberImport $subscriberImport, array $values)
    {
        $this->subscriberImport = $subscriberImport;

        $this->values = $values;

        $this->queue = config('mailcoach.campaigns.perform_on_queue.import_subscribers_job');

        $this->connection = $this->connection ?? Mailcoach::getQueueConnection();
    }

    public function handle()
    {
        $row = new ImportSubscriberRow($this->subscriberImport->emailList, $this->values);

        $lock = Cache::lock("import-subscriber-{$this->subscriberImport->emailList->id}-{$row->getEmail()}", 10);

        try {
            $lock->block(5);

            /** @var \Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ImportSubscriberAction $importSubscriberAction */
            $importSubscriberAction = Mailcoach::getAudienceActionClass('import_subscriber', ImportSubscriberAction::class);

            $importSubscriberAction->execute($this->subscriberImport, $this->values);
        } catch (LockTimeoutException) {
            $this->release(10);
        } finally {
            optional($lock)->release();
        }
    }
}
