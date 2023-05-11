<?php

namespace Spatie\Mailcoach\Domain\Audience\Mails;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Actions\ConvertHtmlToTextAction;
use Spatie\Mailcoach\Domain\Campaign\Mails\Concerns\ReplacesPlaceholders;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\UsesMailcoachTemplate;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

class ConfirmSubscriberMail extends Mailable implements ShouldQueue
{
    use ReplacesPlaceholders;
    use UsesMailcoachTemplate;

    public $theme = 'mailcoach::mails.layout.mailcoach';

    public Subscriber $subscriber;

    public string $confirmationUrl;

    public ?TransactionalMail $confirmationMailTemplate = null;

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
        $this->confirmationMailTemplate = $this->subscriber->emailList->confirmationMail;

        if ($this->confirmationMailTemplate) {
            $this->template($this->confirmationMailTemplate->name);
        }

        $mail = $this
            ->from(
                $this->subscriber->emailList->default_from_email,
                $this->subscriber->emailList->default_from_name
            )
            ->determineSubject()
            ->determineContent();

        $mail->subject($this->replacePlaceholders($mail->subject));

        if ($this->confirmationMailTemplate) {
            $html = $this->confirmationMailTemplate->render($this);
            $html = str_ireplace('::confirmUrl::', $this->confirmationUrl, $html);
            $content = $this->replacePlaceholders($html);
            $plaintext = app(ConvertHtmlToTextAction::class)->execute($content);

            $this
                ->html($content)
                ->text('mailcoach::mails.transactionalMails.template', ['content' => $plaintext]);
        }

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
        if ($this->confirmationMailTemplate) {
            return $this;
        }

        $this->subject(__mc('Confirm your subscription to :emailListName', ['emailListName' => $this->subscriber->emailList->name]));

        return $this;
    }

    protected function determineContent(): self
    {
        if ($this->confirmationMailTemplate) {
            return $this;
        }

        $this->markdown('mailcoach::mails.confirmSubscription');
        $this->text('mailcoach::mails.confirmSubscriptionText');

        return $this;
    }
}
