<?php

namespace Spatie\Mailcoach\Components;

use Illuminate\View\Component;
use Spatie\Mailcoach\Domain\Automation\Support\Replacers\ReplacerWithHelpText;

class AutomationMailReplacerHelpTextsComponent extends Component
{
    public function replacerHelpTexts(): array
    {
        return collect(config('mailcoach.automation.replacers'))
            ->map(fn (string $className) => resolve($className))
            ->flatMap(fn (ReplacerWithHelpText $replacer) => $replacer->helpText())
            ->toArray();
    }

    public function render()
    {
        return view('mailcoach::app.components.replacerHelpTexts');
    }
}
