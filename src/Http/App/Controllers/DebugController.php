<?php

namespace Spatie\Mailcoach\Http\App\Controllers;

use Composer\InstalledVersions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus;
use Spatie\Mailcoach\Domain\Shared\Support\Version;

class DebugController
{
    public function __invoke(HorizonStatus $horizonStatus)
    {
        $versionInfo = resolve(Version::class);
        $hasQueueConnection = config('queue.connections.mailcoach-redis') && ! empty(config('queue.connections.mailcoach-redis'));
        $mysqlVersion = $this->mysqlVersion();
        $horizonVersion = InstalledVersions::getVersion("laravel/horizon");
        $webhookTableCount = DB::table('webhook_calls')
            ->where('name', 'like', '%-feedback')
            ->whereNull('processed_at')
            ->count();
        $lastScheduleRun = Cache::get('mailcoach-last-schedule-run');
        $usesVapor = InstalledVersions::isInstalled("laravel/vapor-core");

        return view('mailcoach::app.debug', compact(
            'versionInfo',
            'horizonStatus',
            'hasQueueConnection',
            'mysqlVersion',
            'horizonVersion',
            'webhookTableCount',
            'lastScheduleRun',
            'usesVapor',
        ));
    }

    private function mysqlVersion(): string
    {
        $results = DB::select('select version() as version');

        return (string) $results[0]->version;
    }
}
