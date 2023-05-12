<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;

class SegmentSubscribersIndexController
{
    use AuthorizesRequests;

    public function __invoke(EmailList $emailList, TagSegment $segment)
    {
        $this->authorize('view', $emailList);

        $selectedSubscribersCount = $segment->getSubscribersQuery()->count();

        return view('mailcoach::app.emailLists.segments.subscribers', [
            'emailList' => $emailList,
            'segment' => $segment,
            'subscribers' => $segment->getSubscribersQuery()->paginate(),
            'selectedSubscribersCount' => $selectedSubscribersCount,
        ]);
    }
}
