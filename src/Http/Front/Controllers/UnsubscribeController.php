<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class UnsubscribeController
{
    use UsesMailcoachModels;

    public function show(string $subscriberUuid, string $sendUuid = null)
    {
        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
        if (! $subscriber = $this->getSubscriberClass()::findByUuid($subscriberUuid)) {
            return view('mailcoach::landingPages.couldNotFindSubscription');
        }

        $emailList = $subscriber->emailList;

        if ($subscriber->status === SubscriptionStatus::Unsubscribed) {
            return view('mailcoach::landingPages.alreadyUnsubscribed', compact('emailList'));
        }

        $send = $subscriber->sends()->where('uuid', $sendUuid)->first();

        return view('mailcoach::landingPages.unsubscribe', compact('emailList', 'subscriber', 'send'));
    }

    public function confirm(string $subscriberUuid, string $sendUuid = null)
    {
        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
        if (! $subscriber = $this->getSubscriberClass()::findByUuid($subscriberUuid)) {
            return view('mailcoach::landingPages.couldNotFindSubscription');
        }

        $emailList = $subscriber->emailList;

        if ($subscriber->status === SubscriptionStatus::Unsubscribed) {
            return view('mailcoach::landingPages.alreadyUnsubscribed', compact('emailList'));
        }

        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
        $send = $this->getSendClass()::findByUuid($sendUuid ?? '');
        $subscriber->unsubscribe($send);

        $emailList = $subscriber->emailList;

        return $emailList->redirect_after_unsubscribed
            ? redirect()->to($emailList->redirect_after_unsubscribed)
            : view('mailcoach::landingPages.unsubscribed', compact('emailList', 'subscriber'));
    }
}
