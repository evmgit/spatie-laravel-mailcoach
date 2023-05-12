<?php

namespace Spatie\Mailcoach\Domain\Audience\Enums;

class SubscriberImportStatus
{
    public const DRAFT = 'draft';
    public const PENDING = 'pending';
    public const IMPORTING = 'importing';
    public const COMPLETED = 'completed';
}
