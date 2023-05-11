<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Replacers;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\Concerns\ReplacesModelAttributes;

class AutomationMailNameAutomationMailReplacer implements AutomationMailReplacer
{
    use ReplacesModelAttributes;

    public function helpText(): array
    {
        return [
            'automation_mail.name' => __mc('The name of this automation mail'),
        ];
    }

    public function replace(string $text, AutomationMail $automationMail): string
    {
        return $this->replaceModelAttributes($text, 'automationMail', $automationMail);
    }
}
