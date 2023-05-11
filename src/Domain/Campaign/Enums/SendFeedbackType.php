<?php

namespace Spatie\Mailcoach\Domain\Campaign\Enums;

enum SendFeedbackType: string
{
    case Bounce = 'bounce';
    case Complaint = 'complaint';
}
