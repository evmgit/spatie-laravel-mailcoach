<?php

namespace Spatie\Mailcoach\Http\App\Controllers;

use Composer\InstalledVersions;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus;
use Spatie\Mailcoach\Domain\Shared\Support\Version;

class DebugController
{
    public function __invoke(HorizonStatus $horizonStatus)
    {
        $versionInfo = resolve(Version::class);
        $hasQueueConnection = config('queue.connections.mailcoach-redis') && ! empty(config('queue.connections.mailcoach-redis'));
        $mysqlVersion = $this->mysqlVersion();
        $horizonVersion = InstalledVersions::getVersion('laravel/horizon');
        $webhookTableCount = DB::table('webhook_calls')
            ->where('name', 'like', '%-feedback')
            ->whereNull('processed_at')
            ->count();
        $lastScheduleRun = Cache::get('mailcoach-last-schedule-run');
        $usesVapor = InstalledVersions::isInstalled('laravel/vapor-core');
        $scheduledJobs = $this->getScheduledJobs();
        $filesystems = $this->getFilesystems();

        return view('mailcoach::app.debug', compact(
            'versionInfo',
            'horizonStatus',
            'hasQueueConnection',
            'mysqlVersion',
            'horizonVersion',
            'webhookTableCount',
            'lastScheduleRun',
            'usesVapor',
            'scheduledJobs',
            'filesystems',
        ));
    }

    private function mysqlVersion(): string
    {
        $results = DB::select('select version() as version');

        return (string) $results[0]->version;
    }

    /** @return Collection<Event> */
    private function getScheduledJobs(): Collection
    {
        app()->make(Kernel::class);
        $schedule = app()->make(Schedule::class);

        return collect($schedule->events())
            ->filter(fn ($event) => Str::contains($event->command, 'mailcoach'));
    }

    private function getFilesystems(): array
    {
        $disks = [
            'import_subscribers_disk' => config('mailcoach.audience.import_subscribers_disk'),
            'import_disk' => config('mailcoach.import_disk'),
            'export_disk' => config('mailcoach.export_disk'),
            'tmp_disk' => config('mailcoach.tmp_disk'),
        ];

        return array_map(
            fn ($disk) => [
                'disk' => $disk,
                'visibility' => config("filesystems.disks.{$disk}.visibility", 'private'),
            ],
            $disks
        );
    }
}
