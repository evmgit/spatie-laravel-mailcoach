<?php

namespace Spatie\Mailcoach\Domain\Campaign\Mails;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Mails\Concerns\ReplacesPlaceholders;

class WelcomeMail extends Mailable implements ShouldQueue
{
    use SerializesModels;
    use ReplacesPlaceholders;

    public $theme = 'mailcoach::mails.layout.mailcoach';

    public Subscriber $subscriber;

    public function __construct(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
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
        $customSubject = $this->subscriber->emailList->welcome_mail_subject;

        $subject = empty($customSubject)
            ? __('Welcome to :emailListName', ['emailListName' => $this->subscriber->emailList->name])
            : $this->replacePlaceholders($customSubject);

        $this->subject($subject);

        return $this;
    }

    protected function determineContent(): self
    {
        $customContent = $this->subscriber->emailList->welcome_mail_content;

        if (! empty($customContent)) {
            $customContent = $this->replacePlaceholders($customContent);

            $customContent = str_ireplace('::unsubscribeUrl::', $this->subscriber->unsubscribeUrl(), $customContent);

            $this->view('mailcoach::mails.customContent', ['content' => $customContent]);

            return $this;
        }

        $this->markdown('mailcoach::mails.welcome');

        return $this;
    }
}
