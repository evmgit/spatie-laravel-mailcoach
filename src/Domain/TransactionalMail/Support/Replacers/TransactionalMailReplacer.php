<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Support\Replacers;

use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

interface TransactionalMailReplacer
{
    public function helpText(): array;

    public function replace(string $templateText, Mailable $mailable, TransactionalMail $template): string;
}
