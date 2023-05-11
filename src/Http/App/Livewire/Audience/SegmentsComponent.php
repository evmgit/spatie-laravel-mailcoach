<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Queries\SegmentsQuery;
use Spatie\Mailcoach\MainNavigation;

class SegmentsComponent extends DataTableComponent
{
    public EmailList $emailList;

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;

        app(MainNavigation::class)->activeSection()?->add($this->emailList->name, route('mailcoach.emailLists.segments', $this->emailList));
    }

    public function duplicateSegment(int $id)
    {
        $this->authorize('update', $this->emailList);

        $segment = self::getTagSegmentClass()::find($id);

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\TagSegment $duplicateSegment */
        $duplicateSegment = self::getTagSegmentClass()::create([
            'name' => __mc('Duplicate of').' '.$segment->name,
            'email_list_id' => $segment->email_list_id,
        ]);

        $positiveTagNames = $segment->positiveTags->map(fn (Tag $tag) => $tag->name)->toArray();
        $duplicateSegment->syncPositiveTags($positiveTagNames);

        $negativeTagNames = $segment->negativeTags->map(fn (Tag $tag) => $tag->name)->toArray();
        $duplicateSegment->syncNegativeTags($negativeTagNames);

        flash()->success(__mc('Segment :segment was duplicated.', ['segment' => $segment->name]));

        return redirect()->route('mailcoach.emailLists.segments.edit', [
            $duplicateSegment->emailList,
            $duplicateSegment,
        ]);
    }

    public function deleteSegment(int $id)
    {
        $segment = self::getTagSegmentClass()::find($id);

        $this->authorize('delete', $segment);

        $segment->delete();

        $this->flash(__mc('Segment :segment was deleted.', ['segment' => $segment->name]));
    }

    public function getTitle(): string
    {
        return __mc('Segments');
    }

    public function getView(): string
    {
        return 'mailcoach::app.emailLists.segments.index';
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.emailLists.layouts.emailList';
    }

    public function getLayoutData(): array
    {
        return [
            'emailList' => $this->emailList,
        ];
    }

    public function getData(Request $request): array
    {
        $this->authorize('view', $this->emailList);

        $segmentsQuery = new SegmentsQuery($this->emailList, $request);

        return [
            'segments' => $segmentsQuery->paginate($request->per_page),
            'emailList' => $this->emailList,
            'totalSegmentsCount' => $this->emailList->segments()->count(),
        ];
    }
}
