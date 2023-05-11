<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails;

use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;
use Spatie\Mailcoach\Http\Api\Resources\TransactionalMailResource;

class ShowTransactionalMailController
{
    public function __invoke(TransactionalMailLogItem $transactionalMail)
    {
        return new TransactionalMailResource($transactionalMail);
    }
}
