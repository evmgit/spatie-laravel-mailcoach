<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateSegmentComponent extends Component
{
    use UsesMailcoachModels;
    use AuthorizesRequests;

    public ?string $name = null;

    public EmailList $emailList;

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;
    }

    protected function rules()
    {
        return [
            'name' => 'required',
        ];
    }

    public function saveSegment()
    {
        $segmentClass = self::getTagSegmentClass();

        $this->authorize('create', $segmentClass);

        $segment = $this->emailList->segments()->create(['name' => $this->validate()['name']]);

        flash()->success(__mc('Segment :segment has been created.', ['segment' => $segment->name]));

        return redirect()->route('mailcoach.emailLists.segments.edit', [$this->emailList, $segment]);
    }

    public function render()
    {
        return view('mailcoach::app.emailLists.segments.partials.create');
    }
}
