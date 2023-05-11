<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportSegmentsJob extends ImportJob
{
    /** @var array<int, int> */
    private array $segmentMapping = [];

    private int $total = 0;

    private int $index = 0;

    /** @var array<string, int> */
    private array $emailLists = [];

    private Collection $tags;

    public function name(): string
    {
        return 'Segments';
    }

    public function execute(): void
    {
        $this->total = $this->getMeta('segments_count', 0) + $this->getMeta('positive_segment_tags_count', 0) + $this->getMeta('negative_segment_tags_count', 0);
        $this->emailLists = self::getEmailListClass()::pluck('id', 'uuid')->toArray();
        $this->tags = self::getTagClass()::all();

        if (! $this->importDisk->exists('import/segments.csv')) {
            return;
        }

        $this->importSegments();
        $this->importPositiveSegmentTags();
        $this->importNegativeSegmentTags();
    }

    private function importSegments(): void
    {
        $this->tmpDisk->writeStream('tmp/segments.csv', $this->importDisk->readStream('import/segments.csv'));

        $reader = SimpleExcelReader::create($this->tmpDisk->path('tmp/segments.csv'));

        foreach ($reader->getRows() as $row) {
            $row['email_list_id'] = $this->emailLists[$row['email_list_uuid']];

            $segment = self::getTagSegmentClass()::firstOrCreate(
                ['name' => $row['name'], 'email_list_id' => $row['email_list_id']],
                array_filter(Arr::except($row, ['id', 'email_list_uuid'])),
            );
            $this->segmentMapping[$row['id']] = $segment->id;

            $this->index++;
            $this->updateJobProgress($this->index, $this->total);
        }

        $this->tmpDisk->delete('tmp/segments.csv');
    }

    private function importPositiveSegmentTags(): void
    {
        if (! $this->importDisk->exists('import/positive_segment_tags.csv')) {
            return;
        }

        $this->tmpDisk->writeStream('tmp/positive_segment_tags.csv', $this->importDisk->readStream('import/positive_segment_tags.csv'));

        $reader = SimpleExcelReader::create($this->tmpDisk->path('tmp/positive_segment_tags.csv'));

        foreach ($reader->getRows() as $row) {
            $row['segment_id'] = $this->segmentMapping[$row['segment_id']];
            $row['tag_id'] = $this->tags->where('name', $row['tag_name'])->where('email_list_id', $this->emailLists[$row['email_list_uuid']])->first()->id;

            DB::table('mailcoach_positive_segment_tags')->updateOrInsert(
                array_filter(Arr::except($row, ['id', 'tag_name', 'email_list_uuid'])),
            );

            $this->index++;
            $this->updateJobProgress($this->index, $this->total);
        }

        $this->tmpDisk->delete('tmp/positive_segment_tags.csv');
    }

    private function importNegativeSegmentTags(): void
    {
        if (! $this->importDisk->exists('import/negative_segment_tags.csv')) {
            return;
        }

        $this->tmpDisk->writeStream('tmp/negative_segment_tags.csv', $this->importDisk->readStream('import/negative_segment_tags.csv'));

        $reader = SimpleExcelReader::create($this->tmpDisk->path('tmp/negative_segment_tags.csv'));

        foreach ($reader->getRows() as $row) {
            $row['segment_id'] = $this->segmentMapping[$row['segment_id']];
            $row['tag_id'] = $this->tags->where('name', $row['tag_name'])->where('email_list_id', $this->emailLists[$row['email_list_uuid']])->first()->id;

            DB::table('mailcoach_negative_segment_tags')->updateOrInsert(
                array_filter(Arr::except($row, ['id', 'tag_name', 'email_list_uuid'])),
            );

            $this->index++;
            $this->updateJobProgress($this->index, $this->total);
        }

        $this->tmpDisk->delete('tmp/negative_segment_tags.csv');
    }
}
