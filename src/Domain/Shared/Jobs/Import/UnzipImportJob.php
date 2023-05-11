<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use ZipArchive;

class UnzipImportJob extends ImportJob
{
    public function __construct(public string $path)
    {
    }

    public function name(): string
    {
        return 'Unzip file';
    }

    public function execute(): void
    {
        if (! $this->importDisk->exists($this->path)) {
            $this->jobFailed("File at {$this->path} does not exist on disk.");

            return;
        }

        $this->tmpDisk->put('import.zip', $this->importDisk->get($this->path));

        $errorCodes = [
            ZipArchive::ER_EXISTS => 'File already exists.',
            ZipArchive::ER_INCONS => 'Zip archive inconsistent.',
            ZipArchive::ER_INVAL => 'Invalid argument.',
            ZipArchive::ER_MEMORY => 'Malloc failure.',
            ZipArchive::ER_NOENT => 'No such file.',
            ZipArchive::ER_NOZIP => 'Not a zip archive.',
            ZipArchive::ER_OPEN => "Can't open file.",
            ZipArchive::ER_READ => 'Read error.',
            ZipArchive::ER_SEEK => 'Seek error.',
        ];

        $zip = new ZipArchive();
        $result = $zip->open($this->tmpDisk->path('import.zip'));

        if ($result !== true) {
            $message = $errorCodes[$result] ?? 'Unknown error.';

            throw new \Exception("Could not open zip file: {$message}");
        }

        $zip->extractTo($this->tmpDisk->path('tmp/import'));
        $zip->close();

        $this->importDisk->deleteDirectory('import');
        $this->importDisk->makeDirectory('import');

        $files = $this->tmpDisk->allFiles('tmp/import');
        foreach ($files as $file) {
            $this->importDisk->writeStream(str_replace('tmp/', '', $file), $this->tmpDisk->readStream($file));
        }
        $this->tmpDisk->deleteDirectory('tmp/import');

        $this->importDisk->delete($this->path);
    }
}
