<?php

namespace Spatie\Mailcoach\Domain\Campaign\Enums;

class CampaignStatus
{
    const DRAFT = 'draft';
    const SENDING = 'sending';
    const SENT = 'sent';
    const CANCELLED = 'cancelled';
}
