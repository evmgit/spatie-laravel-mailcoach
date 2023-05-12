<?php

namespace Spatie\Mailcoach\Domain\Audience\Support;

use Illuminate\Support\Facades\Validator;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\CreateSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Exceptions\CouldNotSubscribe;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Support\Config;

class PendingSubscriber
{
    public string $email;

    public array $attributes = [];

    public bool $respectDoubleOptIn = true;

    public EmailList $emailList;

    public string $redirectAfterSubscribed = '';

    public bool $sendWelcomeMail = true;

    public ?array $tags = [];

    public bool $replaceTags = false;

    public function __construct(string $email, array $attributes = [])
    {
        $this->email = $email;

        if (Validator::make(compact('email'), ['email' => 'email'])->fails()) {
            throw CouldNotSubscribe::invalidEmail($email);
        }

        $this->attributes = $attributes;
    }

    public function withAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function skipConfirmation(): self
    {
        $this->respectDoubleOptIn = false;

        return $this;
    }

    public function redirectAfterSubscribed(string $redirectUrl): self
    {
        $this->redirectAfterSubscribed = $redirectUrl;

        return $this;
    }

    public function doNotSendWelcomeMail(): self
    {
        $this->sendWelcomeMail = false;

        return $this;
    }

    public function tags(?array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function replaceTags(bool $replaceTags = true): self
    {
        $this->replaceTags = $replaceTags;

        return $this;
    }

    public function appendTags(): self
    {
        $this->replaceTags = false;

        return $this;
    }

    public function subscribeTo(EmailList $emailList): Subscriber
    {
        $this->emailList = $emailList;

        /** @var \Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\CreateSubscriberAction $createSubscriberAction */
        $createSubscriberAction = Config::getAudienceActionClass('create_subscriber', CreateSubscriberAction::class);

        return $createSubscriberAction->execute($this);
    }
}
