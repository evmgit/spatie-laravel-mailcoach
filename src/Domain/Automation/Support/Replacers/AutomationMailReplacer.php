<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Replacers;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;

interface AutomationMailReplacer extends ReplacerWithHelpText
{
    public function replace(string $text, AutomationMail $automationMail): string;
}
