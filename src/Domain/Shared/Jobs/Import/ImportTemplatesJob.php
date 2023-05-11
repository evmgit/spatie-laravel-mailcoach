<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Arr;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportTemplatesJob extends ImportJob
{
    public function name(): string
    {
        return 'Templates';
    }

    public function execute(): void
    {
        if (! $this->tmpDisk->exists('import/templates.csv')) {
            return;
        }

        $this->tmpDisk->writeStream('tmp/templates.csv', $this->importDisk->readStream('import/templates.csv'));

        $reader = SimpleExcelReader::create($this->tmpDisk->path('tmp/templates.csv'));

        $total = $this->getMeta('templates_count', 0);
        foreach ($reader->getRows() as $index => $row) {
            self::getTemplateClass()::firstOrCreate(
                ['name' => $row['name'], 'html' => $row['html']],
                Arr::except($row, ['id'])
            );

            $this->updateJobProgress($index, $total);
        }

        $this->tmpDisk->delete('tmp/templates.csv');
    }
}
