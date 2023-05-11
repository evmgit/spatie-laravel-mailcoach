<?php

namespace Spatie\Mailcoach\Domain\Audience\Jobs;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;

class UnsubscribeMissingFromImportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $maxExceptions = 3;

    public function retryUntil(): CarbonInterface
    {
        return now()->addHours(4);
    }

    public function __construct(private SubscriberImport $subscriberImport)
    {
    }

    public function handle()
    {
        if ($this->subscriberImport->status !== SubscriberImportStatus::Completed) {
            $this->release(30); // Try again in 30 seconds

            return;
        }

        $this->subscriberImport
            ->emailList
            ->subscribers()
            ->where(function (Builder $query) {
                $query
                    ->where('imported_via_import_uuid', '<>', $this->subscriberImport->uuid)
                    ->orWhereNull('imported_via_import_uuid');
            })
            ->lazyById()
            ->each(fn (Subscriber $subscriber) => $subscriber->unsubscribe());
    }
}
