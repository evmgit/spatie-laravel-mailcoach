<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Events;

use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailOpen;

class TransactionalMailOpenedEvent
{
    public function __construct(
        public TransactionalMailOpen $transactionalMailOpen,
    ) {
    }
}
