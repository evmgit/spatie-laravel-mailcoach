<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\MainNavigation;

class SegmentComponent extends Component
{
    use AuthorizesRequests;
    use LivewireFlash;

    public string $tab = 'details';

    public EmailList $emailList;

    public TagSegment $segment;

    public string $positive_tags_operator;

    public array $positive_tags;

    public string $negative_tags_operator;

    public array $negative_tags;

    protected $listeners = [
        'tags-updated-positive_tags' => 'updatePositiveTags',
        'tags-updated-negative_tags' => 'updateNegativeTags',
    ];

    protected $queryString = [
        'tab' => ['except' => 'details'],
    ];

    protected function rules(): array
    {
        $emailListTagNames = $this->emailList->tags()->pluck('name')->toArray();

        return [
            'segment.name' => 'required',
            'positive_tags_operator' => [Rule::in(['any', 'all'])],
            'positive_tags.*' => [Rule::in($emailListTagNames)],
            'negative_tags_operator' => [Rule::in(['any', 'all'])],
            'negative_tags.*' => [Rule::in($emailListTagNames)],
        ];
    }

    public function updatePositiveTags(array|string $tags)
    {
        $this->positive_tags = Arr::wrap($tags);
    }

    public function updateNegativeTags(array|string $tags)
    {
        $this->negative_tags = Arr::wrap($tags);
    }

    public function mount(EmailList $emailList, TagSegment $segment, MainNavigation $mainNavigation)
    {
        $this->authorize('update', $emailList);
        $this->authorize('update', $segment);

        $this->emailList = $emailList;
        $this->segment = $segment;

        $this->positive_tags = $segment->positiveTags()->pluck('name')->toArray();
        $this->negative_tags = $segment->negativeTags()->pluck('name')->toArray();
        $this->positive_tags_operator = $segment->all_positive_tags_required ? 'all' : 'any';
        $this->negative_tags_operator = $segment->all_negative_tags_required ? 'all' : 'any';

        $mainNavigation->activeSection()
            ?->add($this->emailList->name, route('mailcoach.emailLists.summary', $this->emailList), function ($section) {
                $section->add(__mc('Segments'), route('mailcoach.emailLists.segments', $this->emailList));
            });
    }

    public function save()
    {
        $this->validate();

        $this->segment->update([
            'name' => $this->segment->name,
            'all_positive_tags_required' => $this->positive_tags_operator === 'all',
            'all_negative_tags_required' => $this->negative_tags_operator === 'all',
        ]);

        $this->segment
            ->syncPositiveTags($this->positive_tags ?? [])
            ->syncNegativeTags($this->negative_tags ?? []);

        $this->flash(__mc('The segment has been updated.'));
    }

    public function render(): View
    {
        $selectedSubscribersCount = $this->segment->getSubscribersCount();

        return view('mailcoach::app.emailLists.segments.show', [
            'selectedSubscribersCount' => $selectedSubscribersCount,
        ])->layout('mailcoach::app.emailLists.layouts.emailList', [
            'title' => $this->segment->name,
            'selectedSubscribersCount' => $selectedSubscribersCount,
            'emailList' => $this->emailList,
        ]);
    }
}
