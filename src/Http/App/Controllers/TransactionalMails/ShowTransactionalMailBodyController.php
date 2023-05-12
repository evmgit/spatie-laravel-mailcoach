<?php

namespace Spatie\Mailcoach\Http\App\Controllers\TransactionalMails;

use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

class ShowTransactionalMailBodyController
{
    public function __invoke(TransactionalMail $transactionalMail)
    {
        return $transactionalMail->body;
    }
}
