<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Actions;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

class SendTestForTransactionalMailTemplateAction
{
    public function execute(array $recipients, TransactionalMail $template): void
    {
        $mailable = $template->getMailable();

        $mailable->to = [];
        $mailable->cc = [];
        $mailable->bcc = [];

        Mail::to($recipients)->send($mailable);
    }
}
