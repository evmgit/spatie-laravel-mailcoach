<?php

namespace Spatie\Mailcoach\Domain\Audience\Rules;

use Illuminate\Contracts\Validation\Rule;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;

class EmailListSubscriptionRule implements Rule
{
    protected EmailList $emailList;

    protected string $attribute;

    public function __construct(EmailList $emailList)
    {
        $this->emailList = $emailList;
    }

    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;

        return $this->emailList->getSubscriptionStatus($value) !== SubscriptionStatus::Subscribed;
    }

    public function message()
    {
        return (string) __mc('This email address is already subscribed.');
    }
}
