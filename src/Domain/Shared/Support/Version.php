<?php

namespace Spatie\Mailcoach\Domain\Shared\Support;

use Carbon\CarbonInterval;
use Composer\InstalledVersions;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Version
{
    public static string $versionEndpoint = 'https://mailcoach.app/api/latest-version';

    public function isLatest(string $packageName = 'laravel-mailcoach'): bool
    {
        $latestVersionInfo = $this->getLatestVersionInfo($packageName);

        if ($latestVersionInfo['version'] === 'unknown') {
            return true;
        }

        return version_compare($this->getCurrentVersion($packageName), $latestVersionInfo['version'], '>=');
    }

    public function getFullVersion(string $packageName = 'laravel-mailcoach'): string
    {
        return InstalledVersions::getVersion("spatie/{$packageName}") ?? '';
    }

    public function getHashedFullVersion(string $packageName = 'laravel-mailcoach'): string
    {
        return md5($this->getFullVersion($packageName));
    }

    public function getCurrentVersion(string $packageName = 'laravel-mailcoach'): string
    {
        return Str::before($this->getFullVersion($packageName), '@');
    }

    public function getLatestVersionInfo(string $packageName = 'laravel-mailcoach'): array
    {
        if (! Cache::has("mailcoach-latest-version-attempt-failed-{$packageName}")) {
            try {
                $latestVersionInfo = Cache::remember("mailcoach-latest-version-{$packageName}", (int) CarbonInterval::day()->totalSeconds, function () use ($packageName) {
                    return Http::asJson()->get(static::$versionEndpoint."?package={$packageName}")->json();
                });
            } catch (Exception $exception) {
                Cache::put("mailcoach-latest-version-attempt-failed-{$packageName}", 1, (int) CarbonInterval::day()->totalSeconds);
            }
        }

        $defaults = [
            'version' => 'unknown',
            'released_at' => 'unknown',
        ];

        return array_merge($defaults, $latestVersionInfo ?? []);
    }
}
