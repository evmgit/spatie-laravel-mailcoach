<?php

namespace Spatie\Mailcoach\Domain\Audience\Exceptions;

use Exception;

class CouldNotSubscribe extends Exception
{
    public static function invalidEmail(string $email): self
    {
        return new static("Could not subscribe `{$email}` because it isn't a valid e-mail");
    }
}
