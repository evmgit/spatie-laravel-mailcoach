<?php

namespace Spatie\Mailcoach\Domain\Shared\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteOldExportsCommand extends Command
{
    public $signature = 'mailcoach:delete-old-exports';

    public $description = 'Delete all export older than a couple of days';

    public function handle()
    {
        $disk = Storage::disk(config('mailcoach.export_disk'));

        collect($disk->allFiles('mailcoach-exports'))
            ->each(function (string $path) use ($disk) {
                if ($path === '.gitignore') {
                    return;
                }

                $lastModifiedTimestamp = $disk->lastModified($path);

                $lastModified = Carbon::createFromTimestamp($lastModifiedTimestamp);

                if ($lastModified->startOfDay()->diffInDays() > 3) {
                    $this->comment("Deleting {$path}...");

                    $disk->delete($path);
                }
            });

        $this->info('All done!');
    }
}
