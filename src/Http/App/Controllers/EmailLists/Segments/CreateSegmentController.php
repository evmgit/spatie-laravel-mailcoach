<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Http\App\Queries\CreateSegmentRequest;

class CreateSegmentController
{
    use AuthorizesRequests;

    public function __invoke(EmailList $emailList, CreateSegmentRequest $request)
    {
        $this->authorize('update', $emailList);

        $segment = $emailList->segments()->create(['name' => $request->name]);

        flash()->success(__('Segment :segment has been created.', ['segment' => $segment->name]));

        return redirect()->route('mailcoach.emailLists.segment.edit', [$emailList, $segment]);
    }
}
