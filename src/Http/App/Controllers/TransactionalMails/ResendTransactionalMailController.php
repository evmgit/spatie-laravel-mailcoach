<?php

namespace Spatie\Mailcoach\Http\App\Controllers\TransactionalMails;

use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

class ResendTransactionalMailController
{
    public function show(TransactionalMail $transactionalMail)
    {
        return view('mailcoach::app.transactionalMails.resend', compact('transactionalMail'));
    }

    public function resend(TransactionalMail $transactionalMail)
    {
        $transactionalMail->resend();

        flash()->success('The mail has been resent!');

        return back();
    }
}
