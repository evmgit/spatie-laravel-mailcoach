<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportTransactionalMailTemplatesJob extends ImportJob
{
    public function name(): string
    {
        return 'Transactional Mail Templates';
    }

    public function execute(): void
    {
        if (! $this->importDisk->exists('import/transactional_mail_templates.csv')) {
            return;
        }

        $this->tmpDisk->writeStream('tmp/transactional_mail_templates.csv', $this->importDisk->readStream('import/transactional_mail_templates.csv'));

        $reader = SimpleExcelReader::create($this->tmpDisk->path('tmp/transactional_mail_templates.csv'));
        $columns = Schema::getColumnListing(self::getTransactionalMailTableName());

        $total = $this->getMeta('transactional_mail_templates_count', 0);
        foreach ($reader->getRows() as $index => $row) {
            self::getTransactionalMailClass()::firstOrCreate([
                'name' => $row['name'],
                'subject' => $row['subject'],
                'type' => $row['type'],
            ], array_filter(Arr::except(Arr::only($row, $columns), ['id'])));

            $this->updateJobProgress($index, $total);
        }

        $this->tmpDisk->delete('tmp/transactional_mail_templates.csv');
    }
}
