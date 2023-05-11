<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Models\InactiveSubscriber;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Symfony\Component\HttpFoundation\Response;

class ReConfirmSubscriberController
{
    use UsesMailcoachModels;

    public function __invoke(string $subscriberUuid)
    {
        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
        $subscriber = $this->getSubscriberClass()::findByUuid($subscriberUuid);

        $inactiveSubscriber = InactiveSubscriber::where('subscriber_id')->first();

        if (! $inactiveSubscriber) {
            return $this->couldNotFindSubscriptionResponse();
        }

        $inactiveSubscriber->delete();

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

    public function couldNotFindSubscriptionResponse()
    {
        return view('mailcoach::landingPages.couldNotFindSubscription');
    }
}
