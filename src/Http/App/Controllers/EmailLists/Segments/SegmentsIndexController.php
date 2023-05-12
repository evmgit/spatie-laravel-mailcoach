<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Http\App\Queries\SegmentsQuery;

class SegmentsIndexController
{
    use AuthorizesRequests;

    public function __invoke(EmailList $emailList)
    {
        $this->authorize('view', $emailList);

        $segmentsQuery = new SegmentsQuery($emailList);

        return view('mailcoach::app.emailLists.segments.index', [
            'segments' => $segmentsQuery->paginate(),
            'emailList' => $emailList,
            'totalSegmentsCount' => $emailList->segments()->count(),
        ]);
    }
}
