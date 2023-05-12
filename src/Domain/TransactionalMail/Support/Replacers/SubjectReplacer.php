<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Support\Replacers;

use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;

class SubjectReplacer implements TransactionalMailReplacer
{
    public function helpText(): array
    {
        return [
            'subject' => 'The subject used on the template',
        ];
    }

    public function replace(string $templateText, Mailable $mailable, TransactionalMailTemplate $template): string
    {
        return str_replace('::subject::', $mailable->subject, $templateText);
    }
}
