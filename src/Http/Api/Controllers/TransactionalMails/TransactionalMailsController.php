<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails;

use Spatie\Mailcoach\Http\Api\Resources\TransactionalMailResource;
use Spatie\Mailcoach\Http\App\Queries\TransactionalMailQuery;

class TransactionalMailsController
{
    public function __invoke(TransactionalMailQuery $transactionalMailsQuery)
    {
        return TransactionalMailResource::collection($transactionalMailsQuery->paginate());
    }
}
