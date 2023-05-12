<?php

namespace Spatie\Mailcoach\Domain\Audience\Mails;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Mails\Concerns\ReplacesPlaceholders;

class ConfirmSubscriberMail extends Mailable implements ShouldQueue
{
    use ReplacesPlaceholders;

    public $theme = 'mailcoach::mails.layout.mailcoach';

    public Subscriber $subscriber;

    public string $confirmationUrl;

    public function __construct(Subscriber $subscriber, string $redirectAfterConfirmedUrl = '')
    {
        $this->subscriber = $subscriber;

        $this->confirmationUrl = url(route('mailcoach.confirm', $subscriber->uuid));

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
            ->determineSubject()
            ->determineContent();

        if (! empty($this->subscriber->emailList->default_reply_to_email)) {
            $mail->replyTo(
                $this->subscriber->emailList->default_reply_to_email,
                $this->subscriber->emailList->default_reply_to_name
            );
        }

        return $mail;
    }

    protected function determineSubject(): self
    {
        $customSubject = $this->subscriber->emailList->confirmation_mail_subject;

        $subject = empty($customSubject)
            ? __('Confirm your subscription to :emailListName', ['emailListName' => $this->subscriber->emailList->name])
            : $this->replacePlaceholders($customSubject);

        $this->subject($subject);

        return $this;
    }

    protected function determineContent(): self
    {
        $customContent = $this->subscriber->emailList->confirmation_mail_content;

        if (! empty($customContent)) {
            $customContent = str_ireplace('::confirmUrl::', $this->confirmationUrl, $customContent);

            $customContent = $this->replacePlaceholders($customContent);

            $this->view('mailcoach::mails.customContent', ['content' => $customContent]);

            return $this;
        }

        $this->markdown('mailcoach::mails.confirmSubscription');

        return $this;
    }
}
