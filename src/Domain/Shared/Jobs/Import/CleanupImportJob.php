<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\Mailcoach\Domain\Shared\Events\ImportFinishedEvent;

class CleanupImportJob extends ImportJob
{
    public function name(): string
    {
        return 'Cleanup';
    }

    public function execute(): void
    {
        Storage::disk(config('mailcoach.import_disk'))->deleteDirectory('import');

        event(new ImportFinishedEvent(Cache::get('import-status', [])));
    }
}
