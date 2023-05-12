<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Http\App\Requests\EmailLists\UpdateSegmentRequest;

class EditSegmentController
{
    use AuthorizesRequests;

    public function edit(EmailList $emailList, TagSegment $segment)
    {
        $this->authorize('update', $emailList);

        $selectedSubscribersCount = $segment->getSubscribersQuery()->count();

        return view('mailcoach::app.emailLists.segments.edit', [
            'emailList' => $emailList,
            'segment' => $segment,
            'selectedSubscribersCount' => $selectedSubscribersCount,
        ]);
    }

    public function update(EmailList $emailList, TagSegment $segment, UpdateSegmentRequest $request)
    {
        $this->authorize('update', $emailList);

        $segment->update([
            'name' => $request->name,
            'all_positive_tags_required' => $request->allPositiveTagsRequired(),
            'all_negative_tags_required' => $request->allNegativeTagsRequired(),
        ]);

        $segment
            ->syncPositiveTags($request->positive_tags ?? [])
            ->syncNegativeTags($request->negative_tags ?? []);

        flash()->success(__('The segment has been updated.'));

        return redirect()->route('mailcoach.emailLists.segment.subscribers', [$emailList, $segment]);
    }
}
