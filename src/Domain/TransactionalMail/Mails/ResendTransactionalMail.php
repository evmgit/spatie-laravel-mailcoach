<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Mails;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\StoresMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

class ResendTransactionalMail extends Mailable
{
    use SerializesModels;
    use StoresMail;

    public function __construct(
        public TransactionalMail $originalMail
    ) {
        $this
            ->from($this->convertPersonsToMailableFormat($this->originalMail->from))
            ->to($this->convertPersonsToMailableFormat($this->originalMail->to))
            ->cc($this->convertPersonsToMailableFormat($this->originalMail->cc))
            ->bcc($this->convertPersonsToMailableFormat($this->originalMail->bcc))
            ->subject($this->originalMail->subject)
            ->view('mailcoach::mails.transactionalMailResend');

        if ($this->originalMail->track_opens) {
            $this->trackOpens();
        }

        if ($this->originalMail->track_clicks) {
            $this->trackClicks();
        }
    }

    public function build()
    {
        $this->view('mailcoach::mails.transactionalMails.resend');

        $this->setMailableClassHeader($this->originalMail->mailable_class);
    }

    protected function convertPersonsToMailableFormat(array $persons): array
    {
        return $persons;

        /*
         * @todo Freek, is this still necessary?
        return collect($persons)
            ->mapWithKeys(function (array $person) {
                return [$person['email'] => $person['name'] ?? null];
            })
            ->toArray();
        */
    }
}
