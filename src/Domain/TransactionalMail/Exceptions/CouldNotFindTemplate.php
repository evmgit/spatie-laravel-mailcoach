<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Exceptions;

use Exception;
use Illuminate\Mail\Mailable;

class CouldNotFindTemplate extends Exception
{
    public static function make(string $templateName, Mailable $mailable): self
    {
        $mailableClass = $mailable::class;

        return new static("Could not send mailable `$mailableClass` because no template named `$templateName` could be found.");
    }
}
