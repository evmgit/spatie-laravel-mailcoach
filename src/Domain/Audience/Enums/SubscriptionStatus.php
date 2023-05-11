<?php

namespace Spatie\Mailcoach\Domain\Audience\Enums;

enum SubscriptionStatus: string
{
    case Unconfirmed = 'unconfirmed';
    case Subscribed = 'subscribed';
    case Unsubscribed = 'unsubscribed';
}
