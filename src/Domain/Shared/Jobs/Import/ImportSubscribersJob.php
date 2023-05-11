<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\LazyCollection;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportSubscribersJob extends ImportJob
{
    public function name(): string
    {
        return 'Subscribers';
    }

    public function execute(): void
    {
        $files = collect($this->importDisk->allFiles('import'))
            ->filter(fn (string $file) => str_ends_with($file, '.csv') && str_starts_with($file, 'import/subscribers'))
            ->sort();

        if (! count($files)) {
            return;
        }

        $emailLists = self::getEmailListClass()::pluck('id', 'uuid')->toArray();

        $total = $this->getMeta('subscribers_count', 0);
        $index = 0;
        foreach ($files as $file) {
            $this->tmpDisk->put('tmp/'.$file, $this->importDisk->get($file));

            $reader = SimpleExcelReader::create($this->tmpDisk->path('tmp/'.$file));

            $reader->getRows()->chunk(1000)->each(function (LazyCollection $subscribers) use ($emailLists, $total, &$index) {
                $chunkCount = $subscribers->count();
                $existingSubscriberUuids = self::getSubscriberClass()::whereIn('uuid', $subscribers->pluck('uuid'))->pluck('uuid');

                $subscribers->whereNotIn('uuid', $existingSubscriberUuids)->each(function (array $subscriber) use ($emailLists) {
                    $subscriber['email_list_id'] = $emailLists[$subscriber['email_list_uuid']];
                    $columns = Schema::getColumnListing(self::getSubscriberTableName());

                    dispatch(new ImportSubscriberJob(array_filter(Arr::except(Arr::only($subscriber, $columns), ['id']))));
                });

                $index += $chunkCount;
                $this->updateJobProgress($index, $total);
            });

            $this->tmpDisk->delete('tmp/'.$file);
        }
    }
}
