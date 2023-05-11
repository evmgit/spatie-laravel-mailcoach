<?php

namespace Spatie\Mailcoach\Domain\Audience\Mails;

use Carbon\CarbonInterface;
use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;

class EmailListSummaryMail extends Mailable
{
    public $theme = 'mailcoach::mails.layout.mailcoach';

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList */
    public object $emailList;

    public CarbonInterface $summaryStartDateTime;

    public string $emailListUrl;

    public string $settingsUrl;

    public function __construct(EmailList $emailList, CarbonInterface $summaryStartDateTime)
    {
        $this->emailList = $emailList;

        $this->summaryStartDateTime = $summaryStartDateTime;

        $this->emailListUrl = route('mailcoach.emailLists.subscribers', $this->emailList);
        $this->settingsUrl = route('mailcoach.emailLists.general-settings', $this->emailList);
    }

    public function build()
    {
        $this
            ->from(
                $this->emailList->default_from_email,
                $this->emailList->default_from_name
            )
            ->subject(__mc("A summary of the ':list' list", ['list' => $this->emailList->name]))
            ->markdown('mailcoach::mails.emailListSummary', [
                'summary' => $this->emailList->summarize($this->summaryStartDateTime),
            ]);
    }
}
