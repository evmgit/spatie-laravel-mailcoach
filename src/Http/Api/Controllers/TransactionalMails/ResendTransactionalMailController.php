<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails;

use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;

class ResendTransactionalMailController
{
    use RespondsToApiRequests;

    public function __invoke(TransactionalMail $transactionalMail)
    {
        $transactionalMail->resend();

        return $this->respondOk();
    }
}
