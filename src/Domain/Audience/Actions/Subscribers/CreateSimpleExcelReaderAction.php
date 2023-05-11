<?php

namespace Spatie\Mailcoach\Domain\Audience\Actions\Subscribers;

use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\SimpleExcel\SimpleExcelReader;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use SplFileObject;

class CreateSimpleExcelReaderAction
{
    protected ?TemporaryDirectory $temporaryDirectory = null;

    public function execute(SubscriberImport $subscriberImport): SimpleExcelReader
    {
        $localImportFile = $this->storeLocalImportFile($subscriberImport);

        $extension = strtolower(pathinfo($localImportFile, PATHINFO_EXTENSION));
        $type = 'csv';

        if ($extension === 'xlsx' || $extension === 'xls') {
            $type = 'xlsx';
        }

        app()->terminating(function () {
            $this->getTemporaryDirectory()->delete();
        });

        return SimpleExcelReader::create($localImportFile, $type)
            ->useDelimiter($this->getCsvDelimiter($localImportFile));
    }

    /**
     * Store import file locally and return path to stored file.
     */
    protected function storeLocalImportFile(SubscriberImport $subscriberImport): string
    {
        $file = $subscriberImport->getFirstMedia('importFile');

        if (! $file) {
            throw new \Exception("Subscriber Import {$subscriberImport->id} has no import file.");
        }

        $localImportFile = $this->getTemporaryDirectory()
            ->path("import-file-{$subscriberImport->created_at->format('Y-m-d H:i:s')}.{$file->extension}");

        file_put_contents($localImportFile, stream_get_contents($file->stream()));

        return $localImportFile;
    }

    protected function getTemporaryDirectory(): TemporaryDirectory
    {
        return $this->temporaryDirectory
            ??= new TemporaryDirectory(storage_path('temp'));
    }

    /**
     * @param  string  $filePath
     * @param  int  $checkLines
     * @return string
     */
    protected function getCsvDelimiter(string $filePath, int $checkLines = 3): string
    {
        $delimiters = [',', ';', "\t", '|'];

        $fileObject = new SplFileObject($filePath);
        $results = [];
        $counter = 0;

        while ($fileObject->valid() && $counter <= $checkLines) {
            $line = $fileObject->fgets();

            foreach ($delimiters as $delimiter) {
                $fields = explode($delimiter, $line);
                $totalFields = count($fields);
                if ($totalFields > 1) {
                    if (! empty($results[$delimiter])) {
                        $results[$delimiter] += $totalFields;
                    } else {
                        $results[$delimiter] = $totalFields;
                    }
                }
            }
            $counter++;
        }

        if (! empty($results)) {
            $results = array_keys($results, max($results));

            return $results[0];
        }

        return ',';
    }
}
