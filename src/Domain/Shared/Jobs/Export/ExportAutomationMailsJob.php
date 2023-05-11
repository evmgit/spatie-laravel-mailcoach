<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Export;

use Illuminate\Support\Facades\DB;

class ExportAutomationMailsJob extends ExportJob
{
    /**
     * @param  string  $path
     * @param  array<int>  $selectedAutomationMails
     */
    public function __construct(protected string $path, protected array $selectedAutomationMails)
    {
    }

    public function name(): string
    {
        return 'Automation Mails';
    }

    public function execute(): void
    {
        $automationMails = DB::table(self::getAutomationMailTableName())
            ->whereIn('id', $this->selectedAutomationMails)
            ->get();

        $this->writeFile('automation_mails.csv', $automationMails);
        $this->addMeta('automation_mails_count', $automationMails->count());

        $automationMailLinks = DB::table(self::getAutomationMailLinkTableName())
            ->whereIn('automation_mail_id', $this->selectedAutomationMails)
            ->get();

        $this->writeFile('automation_mail_links.csv', $automationMailLinks);
        $this->addMeta('automation_mail_links_count', $automationMailLinks->count());
    }
}
