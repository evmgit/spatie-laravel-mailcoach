<?php

namespace Spatie\Mailcoach\Domain\Campaign\Mails;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use SerializesModels;

    public $theme = 'mailcoach::mails.layout.mailcoach';

    public function build()
    {
        return $this->markdown('mailcoach::mails.welcome');
    }
}
