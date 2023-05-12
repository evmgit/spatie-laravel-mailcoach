<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Symfony\Component\HttpFoundation\Response;

class ConfirmSubscriberController
{
    use UsesMailcoachModels;

    public function __invoke(string $subscriberUuid)
    {
        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
        $subscriber = $this->getSubscriberClass()::findByUuid($subscriberUuid);

        if (! $subscriber) {
            return $this->couldNotFindSubscriptionResponse();
        }

        if ($subscriber->isSubscribed()) {
            return $this->wasAlreadySubscribedResponse($subscriber);
        }

        $subscriber->confirm();

        return $this->subscriptionConfirmedResponse($subscriber);
    }

    public function subscriptionConfirmedResponse(Subscriber $subscriber): Response
    {
        if (request()->has('redirect')) {
            return redirect()->to(request()->get('redirect'));
        }

        if ($urlFromEmailList = $subscriber->emailList->redirect_after_subscribed) {
            return redirect()->to($urlFromEmailList);
        }

        return response()->view('mailcoach::landingPages.subscribed', compact('subscriber'));
    }

    public function wasAlreadySubscribedResponse(Subscriber $subscriber)
    {
        if (request()->has('redirect')) {
            return redirect()->to(request()->get('redirect'));
        }

        if ($urlFromEmailList = $subscriber->emailList->redirect_after_already_subscribed) {
            return redirect()->to($urlFromEmailList);
        }

        return view('mailcoach::landingPages.alreadySubscribed', compact('subscriber'));
    }

    public function couldNotFindSubscriptionResponse()
    {
        return view('mailcoach::landingPages.couldNotFindSubscription');
    }
}
