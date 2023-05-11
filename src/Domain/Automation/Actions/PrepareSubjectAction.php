<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Actions\ReplacePlaceholdersAction;

class PrepareSubjectAction
{
    public function __construct(
        protected ReplacePlaceholdersAction $replacePlaceholdersAction
    ) {
    }

    public function execute(AutomationMail $automationMail): void
    {
        $automationMail->subject = $this->replacePlaceholdersAction->execute($automationMail->subject, $automationMail);
        $automationMail->save();
    }
}
