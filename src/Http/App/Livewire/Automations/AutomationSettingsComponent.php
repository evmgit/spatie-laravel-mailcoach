<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\EverySubscriberSegment;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\SubscribersWithTagsSegment;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\MainNavigation;

class AutomationSettingsComponent extends Component
{
    use AuthorizesRequests;
    use LivewireFlash;
    use UsesMailcoachModels;

    public Automation $automation;

    public Collection $triggerOptions;

    public Collection $emailLists;

    public Collection $segmentsData;

    public string $segment;

    public ?string $selectedTrigger = null;

    protected function rules(): array
    {
        return [
            'automation.name' => ['required'],
            'automation.email_list_id' => [Rule::exists(self::getEmailListTableName(), 'id')],
            'segment' => [Rule::in(['entire_list', 'segment'])],
            'automation.segment_id' => ['required_if:segment,segment'],
            'selectedTrigger' => ['required', Rule::in(config('mailcoach.automation.flows.triggers'))],
            'automation.repeat_enabled' => ['nullable', 'boolean'],
            'automation.repeat_only_after_halt' => ['nullable', 'boolean'],
        ];
    }

    public function mount(Automation $automation)
    {
        $this->automation = $automation;

        $this->authorize('update', $this->automation);

        $this->triggerOptions = collect(config('mailcoach.automation.flows.triggers'))
            ->mapWithKeys(function (string $trigger) {
                return [$trigger => $trigger::getName()];
            });

        $this->emailLists = self::getEmailListClass()::all();

        $this->segmentsData = $this->emailLists->map(function (EmailList $emailList) {
            return [
                'id' => $emailList->id,
                'name' => $emailList->name,
                'segments' => $emailList->segments()->orderBy('name')->pluck('name', 'id')->toArray(),
                'createSegmentUrl' => route('mailcoach.emailLists.segments', $emailList),
            ];
        });

        $this->segment = $this->automation->notSegmenting() ? 'entire_list' : 'segment';

        $this->selectedTrigger = $this->automation->triggerClass();

        app(MainNavigation::class)->activeSection()?->add($this->automation->name, route('mailcoach.automations'));
    }

    public function save(string $formData)
    {
        parse_str($formData, $data);

        $this->validate();

        $validator = Validator::make($data, $this->selectedTrigger::rules());
        $triggerData = $validator->validate();

        $segmentClass = SubscribersWithTagsSegment::class;

        if ($this->segment === 'entire_list') {
            $segmentClass = EverySubscriberSegment::class;
        }

        if ($this->automation->usingCustomSegment()) {
            $segmentClass = $this->automation->segment_class;
        }

        $this->automation->fill([
            'segment_class' => $segmentClass,
            'segment_id' => $segmentClass === EverySubscriberSegment::class
                ? null
                : $this->automation->segment_id,
        ]);

        $this->automation->save();
        $this->automation->triggerOn($this->selectedTrigger::make($triggerData));

        $this->automation->update(['segment_description' => $this->automation->getSegment()->description()]);

        $this->flash(__mc('Automation :automation was updated.', ['automation' => $this->automation->name]));
    }

    public function render(): View
    {
        return view('mailcoach::app.automations.settings')
            ->layout('mailcoach::app.automations.layouts.automation', [
                'automation' => $this->automation,
                'title' => __mc('Settings'),
            ]);
    }
}
