<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;

class AutomationMailDeliveryController
{
    public function __invoke(AutomationMail $mail)
    {
        return view('mailcoach::app.automations.mails.delivery', [
            'mail' => $mail,
            'links' => $mail->htmlLinks(),
        ]);
    }
}
