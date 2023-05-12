<?php

namespace Spatie\Mailcoach\Http\App\Controllers\TransactionalMails;

use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

class TransactionalMailContentController
{
    public function __invoke(TransactionalMail $transactionalMail)
    {
        return view('mailcoach::app.transactionalMails.content', compact('transactionalMail'));
    }
}
