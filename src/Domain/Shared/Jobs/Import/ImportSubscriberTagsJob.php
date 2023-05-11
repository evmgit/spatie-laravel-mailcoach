<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportSubscriberTagsJob extends ImportJob
{
    public function name(): string
    {
        return 'Subscriber Tags';
    }

    public function execute(): void
    {
        $files = collect($this->importDisk->allFiles('import'))
            ->filter(fn (string $file) => str_ends_with($file, '.csv') && str_starts_with($file, 'import/email_list_subscriber_tags'))
            ->sort();

        if (! count($files)) {
            return;
        }

        $emailLists = self::getEmailListClass()::pluck('id', 'uuid')->toArray();

        $total = $this->getMeta('email_list_subscriber_tags_count', 0);
        $index = 0;

        foreach ($files as $file) {
            $this->tmpDisk->put('tmp/'.$file, $this->importDisk->get($file));

            SimpleExcelReader::create($this->tmpDisk->path('tmp/'.$file))
                ->getRows()
                ->chunk(5_000)
                ->each(function (LazyCollection $subscriberTags) use ($emailLists, $total, &$index) {
                    $subscribers = DB::table(self::getSubscriberTableName())->whereIn('uuid', $subscriberTags->pluck('subscriber_uuid')->unique())->pluck('id', 'uuid')->toArray();
                    $tags = DB::table(self::getTagTableName())
                        ->select('id', DB::raw('CONCAT(email_list_id, "-", name) as unique_key'))
                        ->whereIn('email_list_id', $emailLists)
                        ->pluck('id', 'unique_key')
                        ->toArray();

                    $existingSubscriberTags = DB::table('mailcoach_email_list_subscriber_tags')
                        ->select(DB::raw('CONCAT(tag_id, "-", subscriber_id) as unique_key'))
                        ->whereIn('tag_id', $tags)
                        ->whereIn('subscriber_id', $subscribers)
                        ->pluck('unique_key', 'unique_key')
                        ->toArray();

                    $newSubscriberTags = $subscriberTags->map(function ($row) use ($existingSubscriberTags, $subscribers, $tags, $emailLists) {
                        $emailListId = $emailLists[$row['email_list_uuid']];
                        $tagId = $tags["{$emailListId}-{$row['tag_name']}"];

                        if (! isset($subscribers[$row['subscriber_uuid']])) {
                            return null;
                        }

                        $subscriberId = $subscribers[$row['subscriber_uuid']];

                        if (isset($existingSubscriberTags["{$tagId}-{$subscriberId}"])) {
                            return null;
                        }

                        return [
                            'subscriber_id' => $subscriberId,
                            'tag_id' => $tagId,
                        ];
                    })->filter()->toArray();

                    DB::table('mailcoach_email_list_subscriber_tags')->insert($newSubscriberTags);

                    $index += 5_000;
                    $this->updateJobProgress($index, $total);
                });

            $this->tmpDisk->delete('tmp/'.$file);
        }
    }
}
