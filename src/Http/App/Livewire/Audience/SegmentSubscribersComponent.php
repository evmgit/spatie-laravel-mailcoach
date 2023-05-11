<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;

class SegmentSubscribersComponent extends DataTableComponent
{
    public string $sort = 'email';

    public EmailList $emailList;

    public TagSegment $segment;

    public function mount(EmailList $emailList, TagSegment $segment)
    {
        $this->emailList = $emailList;
        $this->segment = $segment;
    }

    public function getTitle(): string
    {
        return $this->segment->name;
    }

    public function getView(): string
    {
        return 'mailcoach::app.emailLists.segments.subscribers';
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.emailLists.segments.layouts.segment';
    }

    public function getLayoutData(): array
    {
        return [
            'emailList' => $this->emailList,
            'segment' => $this->segment,
            'selectedSubscribersCount' => $this->segment->getSubscribersCount(),
        ];
    }

    public function getData(Request $request): array
    {
        $this->authorize('view', $this->emailList);

        return [
            'emailList' => $this->emailList,
            'segment' => $this->segment,
            'subscribers' => $this->segment->getSubscribersQuery()->with(['tags'])->paginate($request->per_page),
            'subscribersCount' => $this->emailList->subscribers()->count(),
            'selectedSubscribersCount' => $this->segment->getSubscribersCount(),
        ];
    }
}
