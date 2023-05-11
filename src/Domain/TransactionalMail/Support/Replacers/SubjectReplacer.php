<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Support\Replacers;

use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

class SubjectReplacer implements TransactionalMailReplacer
{
    public function helpText(): array
    {
        return [
            'subject' => 'The subject used on the template',
        ];
    }

    public function replace(string $templateText, Mailable $mailable, TransactionalMail $template): string
    {
        return str_replace('::subject::', $mailable->subject, $templateText);
    }
}
