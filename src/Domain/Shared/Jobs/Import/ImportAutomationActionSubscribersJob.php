<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportAutomationActionSubscribersJob extends ImportJob
{
    public function name(): string
    {
        return 'Automation Action-Subscribers';
    }

    public function execute(): void
    {
        $files = collect($this->importDisk->allFiles('import'))
            ->filter(fn (string $file) => str_ends_with($file, '.csv') && str_starts_with($file, 'import/automation_action_subscribers'))
            ->sort();

        if (! count($files)) {
            return;
        }

        $actions = self::getAutomationActionClass()::pluck('id', 'uuid')->toArray();

        $total = $this->getMeta('automation_action_subscribers_count', 0);

        $index = 0;
        foreach ($files as $file) {
            $this->tmpDisk->writeStream('tmp/'.$file, $this->importDisk->readStream($file));

            $reader = SimpleExcelReader::create($this->tmpDisk->path('tmp/'.$file));

            $reader->getRows()->chunk(1000)->each(function (LazyCollection $actionSubscribers) use ($actions, $total, &$index) {
                $subscribers = DB::table(self::getSubscriberTableName())->whereIn('uuid', $actionSubscribers->pluck('subscriber_uuid'))->pluck('id', 'uuid');

                foreach ($actionSubscribers as $row) {
                    $row['action_id'] = $actions[$row['action_uuid']];
                    $row['subscriber_id'] = $subscribers[$row['subscriber_uuid']];

                    if (! self::getActionSubscriberClass()::where([
                        'action_id' => $row['action_id'],
                        'subscriber_id' => $row['subscriber_id'],
                    ])->exists()) {
                        $row['uuid'] ??= Str::uuid();
                        self::getActionSubscriberClass()::create(array_filter(Arr::except($row, ['id', 'subscriber_uuid', 'action_uuid'])));
                    }

                    $index++;
                    $this->updateJobProgress($index, $total);
                }
            });

            $this->tmpDisk->delete('tmp/'.$file);
        }
    }
}
