<?php

namespace Spatie\Mailcoach\Domain\Campaign\Mails;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Mails\Concerns\ReplacesPlaceholders;

class ReconfirmationMail extends Mailable implements ShouldQueue
{
    use ReplacesPlaceholders;

    public $theme = 'mailcoach::mails.layout.mailcoach';

    public Subscriber $subscriber;

    public string $confirmationUrl;

    public function __construct(Subscriber $subscriber, string $redirectAfterConfirmedUrl = '')
    {
        $this->subscriber = $subscriber;

        $this->confirmationUrl = url(route('mailcoach.reconfirm', $subscriber->uuid));

        if ($redirectAfterConfirmedUrl !== '') {
            $this->confirmationUrl .= "?redirect={$redirectAfterConfirmedUrl}";
        }
    }

    public function build()
    {
        $mail = $this
            ->from(
                $this->subscriber->emailList->default_from_email,
                $this->subscriber->emailList->default_from_name
            )
            ->markdown('mailcoach::mails.reconfirmSubscription');

        if (! empty($this->subscriber->emailList->default_reply_to_email)) {
            $mail->replyTo(
                $this->subscriber->emailList->default_reply_to_email,
                $this->subscriber->emailList->default_reply_to_name
            );
        }

        return $mail;
    }
}
