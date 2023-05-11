<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Exceptions;

use Exception;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\UsesMailcoachTemplate;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Support\Replacers\TransactionalMailReplacer;

class InvalidTemplate extends Exception
{
    public static function mailableClassNotFound(TransactionalMail $template): self
    {
        return new self("The class specified in `test_using_mailable` (`{$template->test_using_mailable}`) in the template with name `$template->name` could not be found. Make sure the `test_using_mailable` exists.");
    }

    public static function mailableClassNotValid(TransactionalMail $template): self
    {
        $expectedTrait = UsesMailcoachTemplate::class;

        return new self("The class specified in `test_using_mailable` (`{$template->test_using_mailable}`) in the template with name `$template->name` is invalid. Make sure your mailable uses the `{$expectedTrait}` trait.");
    }

    public static function replacerNotFound(TransactionalMail $template, string $replacerName): self
    {
        return new self("Could not find a replacer named `{$replacerName}` used in the template named `{$template->name}`. Make sure you specify a replacer with that name in the `transactional.replacers` key of the mailcoach config file.");
    }

    public static function invalidReplacer(TransactionalMail $template, string $replacerName, ?string $replacerClass): self
    {
        $replacerInterface = TransactionalMailReplacer::class;

        return new self("The class found (`{$replacerClass}`) for a replacer named `{$replacerName}` for template `{$template->name}` is not valid. Make sure `{$replacerClass}` implements `{$replacerInterface}`");
    }
}
