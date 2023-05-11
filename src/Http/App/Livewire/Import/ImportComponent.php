<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Import;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\CleanupImportJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportAutomationActionSubscribersJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportAutomationMailsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportAutomationsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportCampaignsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportEmailListsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportSegmentsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportSubscribersJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportSubscriberTagsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportTagsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportTemplatesJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportTransactionalMailTemplatesJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\UnzipImportJob;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;

class ImportComponent extends Component
{
    use WithFileUploads;
    use LivewireFlash;

    /** @var \Illuminate\Http\UploadedFile */
    public $file;

    public bool $importStarted = false;

    public function import(): void
    {
        $this->validate([
            'file' => ['file'],
        ]);

        $disk = Storage::disk(config('mailcoach.import_disk'));

        $disk->deleteDirectory('import');
        $disk->makeDirectory('import');

        $path = $this->file->storeAs('import', 'mailcoach-import.zip', [
            'disk' => config('mailcoach.import_disk'),
        ]);

        if (! $path) {
            $this->flashError('Upload failed. Please try again');

            return;
        }

        Bus::chain([
            new UnzipImportJob($path),
            new ImportEmailListsJob(),
            new ImportSubscribersJob(),
            new ImportTagsJob(),
            new ImportSubscriberTagsJob(),
            new ImportSegmentsJob(),
            new ImportTemplatesJob(),
            new ImportCampaignsJob(),
            new ImportAutomationMailsJob(),
            new ImportAutomationsJob(),
            new ImportAutomationActionSubscribersJob(),
            new ImportTransactionalMailTemplatesJob(),
            new CleanupImportJob(),
        ])->onQueue(config('mailcoach.campaigns.perform_on_queue.send_campaign_job'))->dispatch();

        $this->importStarted = true;
    }

    public function clear()
    {
        cache()->forget('import-status');
        $this->importStarted = false;
        $this->emitSelf('$refresh');
    }

    public function render()
    {
        return view('mailcoach::app.import')
            ->layout('mailcoach::app.layouts.app', ['title' => __mc('Import')]);
    }
}
