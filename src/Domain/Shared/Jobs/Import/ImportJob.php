<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Throwable;

abstract class ImportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UsesMailcoachModels;

    abstract public function name(): string;

    abstract public function execute(): void;

    private LazyCollection $metaRows;

    protected Filesystem $importDisk;

    protected Filesystem $tmpDisk;

    protected function getMeta(string $key, mixed $default = null): mixed
    {
        if (! $this->importDisk->exists('import/meta.json')) {
            return $default;
        }

        $meta = json_decode($this->importDisk->get('import/meta.json'), true);

        return $meta[$key] ?? $default;
    }

    protected function jobStarted(): void
    {
        $steps = Cache::get('import-status', []);

        $steps[$this->name()] = [
            'finished' => false,
            'progress' => 0,
            'index' => 0,
            'total' => 0,
            'failed' => false,
            'message' => '',
        ];

        Cache::put('import-status', $steps);
    }

    protected function jobFinished(): void
    {
        $steps = Cache::get('import-status', []);

        $steps[$this->name()]['finished'] = true;
        $steps[$this->name()]['progress'] = 1;

        Cache::put('import-status', $steps);
    }

    protected function updateJobProgress(int $index, int $total): void
    {
        $steps = Cache::get('import-status', []);

        $steps[$this->name()]['progress'] = ($index + 1) / $total;
        $steps[$this->name()]['index'] = $index;
        $steps[$this->name()]['total'] = $total;

        Cache::put('import-status', $steps);
    }

    protected function jobFailed(string $exceptionMessage): void
    {
        $steps = Cache::get('import-status', []);

        $steps[$this->name()]['failed'] = true;
        $steps[$this->name()]['message'] = $exceptionMessage;

        Cache::put('import-status', $steps);
    }

    public function handle(): void
    {
        $this->importDisk = Storage::disk(config('mailcoach.import_disk'));
        $this->tmpDisk = Storage::disk(config('mailcoach.tmp_disk'));

        $this->jobStarted();

        $this->execute();

        $this->jobFinished();
    }

    public function failed(Throwable $exception)
    {
        $this->jobFailed($exception->getMessage());
        dispatch(new CleanupImportJob());
    }
}
