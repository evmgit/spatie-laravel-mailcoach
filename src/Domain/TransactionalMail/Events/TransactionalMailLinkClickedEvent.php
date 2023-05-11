<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Events;

use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailClick;

class TransactionalMailLinkClickedEvent
{
    public function __construct(
        public TransactionalMailClick $campaignClick,
    ) {
    }
}
