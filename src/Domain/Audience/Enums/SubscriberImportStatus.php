<?php

namespace Spatie\Mailcoach\Domain\Audience\Enums;

enum SubscriberImportStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Importing = 'importing';
    case Completed = 'completed';
    case Failed = 'failed';
}
