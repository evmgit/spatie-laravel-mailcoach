<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers;

use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationTriggerComponent;

class TagAddedTriggerComponent extends AutomationTriggerComponent
{
    public function render()
    {
        return view('mailcoach::app.automations.components.triggers.tagAddedTrigger');
    }
}
