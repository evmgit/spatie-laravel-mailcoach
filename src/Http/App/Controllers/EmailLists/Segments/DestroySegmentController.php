<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;

class DestroySegmentController
{
    use AuthorizesRequests;

    public function __invoke(EmailList $emailList, TagSegment $segment)
    {
        $this->authorize('update', $emailList);

        $segment->delete();

        flash()->success(__('Segment :segment was deleted.', ['segment' => $segment->name]));

        return back();
    }
}
