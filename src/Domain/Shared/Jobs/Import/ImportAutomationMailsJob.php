<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportAutomationMailsJob extends ImportJob
{
    /** @var array<int, int> */
    private array $automationMailMapping = [];

    private int $total = 0;

    private int $index = 0;

    public function name(): string
    {
        return 'Automation Mails & Automation Mail Links';
    }

    public function execute(): void
    {
        $this->total = $this->getMeta('automation_mails_count', 0) + $this->getMeta('automation_mail_links_count', 0);

        $this->importAutomationMails();
        $this->importAutomationMailLinks();
    }

    private function importAutomationMails(): void
    {
        if (! $this->importDisk->exists('import/automation_mails.csv')) {
            return;
        }

        $this->tmpDisk->put('tmp/automation_mails.csv', $this->importDisk->get('import/automation_mails.csv'));

        $reader = SimpleExcelReader::create($this->tmpDisk->path('tmp/automation_mails.csv'));
        $columns = Schema::getColumnListing(self::getAutomationMailTableName());

        foreach ($reader->getRows() as $row) {
            $automationMail = self::getAutomationMailClass()::firstOrCreate(
                ['uuid' => $row['uuid']],
                array_filter(Arr::except(Arr::only($row, $columns), ['id'])),
            );

            $this->automationMailMapping[$row['id']] = $automationMail->id;

            $this->index++;
            $this->updateJobProgress($this->index, $this->total);
        }

        $this->tmpDisk->delete('tmp/automation_mails.csv');
    }

    private function importAutomationMailLinks(): void
    {
        if (! $this->importDisk->exists('import/automation_mail_links.csv')) {
            return;
        }

        $this->tmpDisk->writeStream('tmp/automation_mail_links.csv', $this->importDisk->readStream('import/automation_mail_links.csv'));

        $reader = SimpleExcelReader::create($this->tmpDisk->path('tmp/automation_mail_links.csv'));
        foreach ($reader->getRows() as $row) {
            $row['automation_mail_id'] = $this->automationMailMapping[$row['automation_mail_id']];

            self::getAutomationMailLinkClass()::firstOrCreate(
                ['automation_mail_id' => $row['automation_mail_id'], 'url' => $row['url']],
                array_filter(Arr::except($row, ['id'])),
            );

            $this->index++;
            $this->updateJobProgress($this->index, $this->total);
        }

        $this->tmpDisk->delete('tmp/automation_mail_links.csv');
    }
}
