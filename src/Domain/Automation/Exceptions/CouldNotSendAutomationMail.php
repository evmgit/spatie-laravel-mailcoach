<?php

namespace Spatie\Mailcoach\Domain\Automation\Exceptions;

use Exception;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;

class CouldNotSendAutomationMail extends Exception
{
    public static function invalidContent(AutomationMail $automationMail, Exception $errorException): self
    {
        return new static("The automation mail with id `{$automationMail->id}` can't be sent because the content isn't valid. Please check if the html is valid. DOMDocument reported: `{$errorException->getMessage()}`", 0, $errorException);
    }

    public static function invalidMailableClass(AutomationMail $automationMail, string $invalidMailableClass): self
    {
        $mustExtend = MailcoachMail::class;

        return new static("The campaign with id `{$automationMail->id}` can't be sent, because an invalid mailable class `{$invalidMailableClass}` is set. A valid mailable class must extend `{$mustExtend}`.");
    }
}
