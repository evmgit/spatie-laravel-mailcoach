<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Export;

use Illuminate\Support\Facades\DB;

class ExportSegmentsJob extends ExportJob
{
    /**
     * @param  string  $path
     * @param  array<int>  $selectedEmailLists
     */
    public function __construct(protected string $path, protected array $selectedEmailLists)
    {
    }

    public function name(): string
    {
        return 'Segments';
    }

    public function execute(): void
    {
        $segments = DB::table(self::getTagSegmentTableName())
            ->select(self::getTagSegmentTableName().'.*', DB::raw(self::getEmailListTableName().'.uuid as email_list_uuid'))
            ->join(self::getEmailListTableName(), self::getEmailListTableName().'.id', '=', self::getTagSegmentTableName().'.email_list_id')
            ->whereIn('email_list_id', $this->selectedEmailLists)
            ->get();

        $this->writeFile('segments.csv', $segments);
        $this->addMeta('segments_count', $segments->count());

        $positiveTags = DB::table('mailcoach_positive_segment_tags')
            ->select(
                'mailcoach_positive_segment_tags.*',
                DB::raw(self::getTagTableName().'.name as tag_name'),
                DB::raw(self::getEmailListTableName().'.uuid as email_list_uuid'),
            )
            ->join(self::getTagSegmentTableName(), self::getTagSegmentTableName().'.id', '=', 'mailcoach_positive_segment_tags.segment_id')
            ->join(self::getTagTableName(), self::getTagTableName().'.id', '=', 'mailcoach_positive_segment_tags.tag_id')
            ->join(self::getEmailListTableName(), self::getEmailListTableName().'.id', '=', self::getTagSegmentTableName().'.email_list_id')
            ->whereIn(self::getTagSegmentTableName().'.email_list_id', $this->selectedEmailLists)
            ->get();

        $this->writeFile('positive_segment_tags.csv', $positiveTags);
        $this->addMeta('positive_segment_tags_count', $positiveTags->count());

        $negativeTags = DB::table('mailcoach_negative_segment_tags')
            ->select(
                'mailcoach_negative_segment_tags.*',
                DB::raw(self::getTagTableName().'.name as tag_name'),
                DB::raw(self::getEmailListTableName().'.uuid as email_list_uuid'),
            )
            ->join(self::getTagSegmentTableName(), self::getTagSegmentTableName().'.id', '=', 'mailcoach_negative_segment_tags.segment_id')
            ->join(self::getTagTableName(), self::getTagTableName().'.id', '=', 'mailcoach_negative_segment_tags.tag_id')
            ->join(self::getEmailListTableName(), self::getEmailListTableName().'.id', '=', self::getTagSegmentTableName().'.email_list_id')
            ->whereIn(self::getTagSegmentTableName().'.email_list_id', $this->selectedEmailLists)
            ->get();

        $this->writeFile('negative_segment_tags.csv', $negativeTags);
        $this->addMeta('negative_segment_tags_count', $negativeTags->count());
    }
}
