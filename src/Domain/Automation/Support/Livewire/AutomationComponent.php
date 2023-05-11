<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

abstract class AutomationComponent extends Component
{
    use UsesMailcoachModels;

    public Automation $automation;

    abstract public function render();
}
