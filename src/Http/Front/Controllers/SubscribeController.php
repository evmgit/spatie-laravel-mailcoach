<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Front\Requests\CreateSubscriptionRequest;
use Symfony\Component\HttpFoundation\Response;

class SubscribeController
{
    use UsesMailcoachModels;

    public function show()
    {
        return view('mailcoach::landingPages.subscribeInfo');
    }

    public function store(CreateSubscriptionRequest $request)
    {
        $emailList = $request->emailList();

        if ($emailList->honeypot_field && $request->get($emailList->honeypot_field)) {
            $subscriberClass = self::getSubscriberClass();

            return $this->getSubscribedResponse($request, $emailList, new $subscriberClass);
        }

        if ($emailList->getSubscriptionStatus($request->email) === SubscriptionStatus::Subscribed) {
            $subscriber = self::getSubscriberClass()::findForEmail($request->email, $emailList);
            $subscriber->addTags($request->tags());

            return $this->getAlreadySubscribedResponse($request, $emailList);
        }

        $subscriber = self::getSubscriberClass()::createWithEmail($request->email)
            ->withAttributes($request->subscriberAttributes())
            ->redirectAfterSubscribed($request->redirect_after_subscribed ?? '')
            ->tags($request->tags())
            ->replaceTags()
            ->subscribeTo($emailList);

        $subscriber->extra_attributes = array_merge($subscriber->extra_attributes->toArray(), $request->attributes());

        $subscriber->save();

        return $subscriber->isUnconfirmed()
            ? $this->getSubscriptionPendingResponse($request, $emailList, $subscriber)
            : $this->getSubscribedResponse($request, $emailList, $subscriber);
    }

    protected function getSubscriptionPendingResponse(CreateSubscriptionRequest $request, EmailList $emailList, Subscriber $subscriber): Response
    {
        if ($request->redirect_after_subscription_pending) {
            return redirect()->to($request->redirect_after_subscription_pending);
        }

        if ($urlFromEmailList = $emailList->redirect_after_subscription_pending) {
            return redirect()->to($urlFromEmailList);
        }

        $subscriber->load('emailList');

        return response()->view('mailcoach::landingPages.confirmSubscription', compact('subscriber'));
    }

    protected function getSubscribedResponse(CreateSubscriptionRequest $request, EmailList $emailList, Subscriber $subscriber): Response
    {
        if ($request->redirect_after_subscribed) {
            return redirect()->to($request->redirect_after_subscribed);
        }

        if ($urlFromEmailList = $emailList->redirect_after_subscribed) {
            return redirect()->to($urlFromEmailList);
        }

        $subscriber->load('emailList');

        return response()->view('mailcoach::landingPages.subscribed', compact('subscriber'));
    }

    protected function getAlreadySubscribedResponse(CreateSubscriptionRequest $request, EmailList $emailList): Response
    {
        if ($urlFromRequest = $request->redirect_after_already_subscribed) {
            return redirect()->to($urlFromRequest);
        }

        if ($urlFromEmailList = $emailList->redirect_after_already_subscribed) {
            return redirect()->to($urlFromEmailList);
        }

        return response()->view('mailcoach::landingPages.alreadySubscribed');
    }
}
