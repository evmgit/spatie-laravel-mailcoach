<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Events;

use Illuminate\Mail\Events\MessageSending;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

class TransactionalMailStored
{
    public function __construct(
        public TransactionalMail $transactionalMail,
        public MessageSending $sending
    ) {
    }
}
