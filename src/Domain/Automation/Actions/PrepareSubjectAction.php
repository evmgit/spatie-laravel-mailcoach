<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Replacers\AutomationMailReplacer;

class PrepareSubjectAction
{
    public function execute(AutomationMail $automationMail): void
    {
        $this->replacePlaceholdersInSubject($automationMail);

        $automationMail->save();
    }

    protected function replacePlaceholdersInSubject(AutomationMail $automationMail): void
    {
        $automationMail->subject = collect(config('mailcoach.automation.replacers'))
            ->map(fn (string $className) => resolve($className))
            ->filter(fn (object $class) => $class instanceof AutomationMailReplacer)
            ->reduce(fn (string $subject, AutomationMailReplacer $replacer) => $replacer->replace($subject, $automationMail), $automationMail->subject);
    }
}
