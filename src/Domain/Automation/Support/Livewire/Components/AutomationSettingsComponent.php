<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Components;

use Illuminate\Support\Collection;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class AutomationSettingsComponent extends Component
{
    use UsesMailcoachModels;

    public Automation $automation;

    public Collection $triggerOptions;

    public Collection $emailLists;

    public Collection $segmentsData;

    public string $selectedTrigger;

    public function mount()
    {
        $this->triggerOptions = collect(config('mailcoach.automation.flows.triggers'))
            ->mapWithKeys(function (string $trigger) {
                return [$trigger => $trigger::getName()];
            });

        $this->emailLists = $this->getEmailListClass()::all();

        $this->segmentsData = $this->emailLists->map(function (EmailList $emailList) {
            return [
                'id' => $emailList->id,
                'name' => $emailList->name,
                'segments' => $emailList->segments->map->only('id', 'name'),
                'createSegmentUrl' => route('mailcoach.emailLists.segments', $emailList),
            ];
        });

        $this->selectedTrigger = old('trigger', $this->automation->triggerClass());
    }

    public function setSelectedTrigger(string $triggerClass): void
    {
        $this->selectedTrigger = $triggerClass;
    }

    public function render()
    {
        return view('mailcoach::app.automations.partials.settingsForm');
    }
}
