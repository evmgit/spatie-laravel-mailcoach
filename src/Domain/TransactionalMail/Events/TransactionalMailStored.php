<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Events;

use Illuminate\Mail\Events\MessageSending;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;

class TransactionalMailStored
{
    public function __construct(
        public TransactionalMailLogItem $transactionalMail,
        public MessageSending $sending
    ) {
    }
}
