<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;

class DuplicateSegmentController
{
    use AuthorizesRequests;

    public function __invoke(EmailList $emailList, TagSegment $segment)
    {
        $this->authorize('update', $emailList);

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\TagSegment $duplicateSegment */
        $duplicateSegment = TagSegment::create([
            'name' => __('Duplicate of') . ' ' . $segment->name,
            'email_list_id' => $segment->email_list_id,
        ]);

        $positiveTagNames = $segment->positiveTags->map(fn (Tag $tag) => $tag->name)->toArray();
        $duplicateSegment->syncPositiveTags($positiveTagNames);

        $negativeTagNames = $segment->negativeTags->map(fn (Tag $tag) => $tag->name)->toArray();
        $duplicateSegment->syncNegativeTags($negativeTagNames);

        flash()->success(__('Segment :segment was duplicated.', ['segment' => $segment->name]));

        return redirect()->route('mailcoach.emailLists.segment.edit', [
            $duplicateSegment->emailList,
            $duplicateSegment,
        ]);
    }
}
