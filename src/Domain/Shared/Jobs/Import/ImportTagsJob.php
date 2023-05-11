<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportTagsJob extends ImportJob
{
    /** @var array<int, int> */
    private array $tagMapping = [];

    public function name(): string
    {
        return 'Tags';
    }

    public function execute(): void
    {
        if (! $this->importDisk->exists('import/tags.csv')) {
            return;
        }

        $emailLists = self::getEmailListClass()::pluck('id', 'uuid');

        $total = $this->getMeta('tags_count', 0) + $this->getMeta('email_list_allow_form_subscription_tags_count', 0);

        $this->tmpDisk->writeStream('tmp/tags.csv', $this->importDisk->readStream('import/tags.csv'));

        $reader = SimpleExcelReader::create($this->tmpDisk->path('tmp/tags.csv'));
        $index = 0;

        foreach ($reader->getRows() as $row) {
            $row['email_list_id'] = $emailLists[$row['email_list_uuid']] ?? null;

            $tag = self::getTagClass()::firstOrCreate([
                'name' => $row['name'],
                'email_list_id' => $row['email_list_id'],
            ], array_filter(Arr::except($row, ['id', 'email_list_uuid'])));

            $this->tagMapping[$row['id']] = $tag->id;

            $index++;
            $this->updateJobProgress($index, $total);
        }

        if (! $this->importDisk->exists('import/email_list_allow_form_subscription_tags.csv')) {
            return;
        }

        $this->tmpDisk->writeStream('tmp/email_list_allow_form_subscription_tags.csv', $this->importDisk->readStream('import/email_list_allow_form_subscription_tags.csv'));

        $reader = SimpleExcelReader::create($this->tmpDisk->path('tmp/email_list_allow_form_subscription_tags.csv'));
        foreach ($reader->getRows() as $row) {
            $row['email_list_id'] = $emailLists[$row['email_list_uuid']];
            $row['tag_id'] = $this->tagMapping[$row['tag_id']] ?? null;

            DB::table('mailcoach_email_list_allow_form_subscription_tags')->updateOrInsert([
                'email_list_id' => $row['email_list_id'],
                'tag_id' => $row['tag_id'],
            ]);

            $index++;
            $this->updateJobProgress($index, $total);
        }

        $this->tmpDisk->delete('tmp/email_list_allow_form_subscription_tags.csv');
    }
}
