<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers;

use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationTriggerComponent;

class DateTriggerComponent extends AutomationTriggerComponent
{
    public ?array $date = null;

    public function render()
    {
        return view('mailcoach::app.automations.components.triggers.dateTrigger');
    }
}
