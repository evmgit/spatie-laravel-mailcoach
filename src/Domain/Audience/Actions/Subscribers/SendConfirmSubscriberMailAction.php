<?php

namespace Spatie\Mailcoach\Domain\Audience\Actions\Subscribers;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Events\UnconfirmedSubscriberCreatedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Mailcoach;

class SendConfirmSubscriberMailAction
{
    public function execute(Subscriber $subscriber, string $redirectAfterSubscribed = ''): void
    {
        if (! $subscriber->isUnconfirmed()) {
            return;
        }

        $mailableClass = $subscriber->emailList->confirmSubscriberMailableClass();

        Mail::mailer($subscriber->emailList->transactional_mailer ?? Mailcoach::defaultTransactionalMailer())
            ->to($subscriber->email)
            ->queue(new $mailableClass($subscriber, $redirectAfterSubscribed));

        event(new UnconfirmedSubscriberCreatedEvent($subscriber));
    }
}
