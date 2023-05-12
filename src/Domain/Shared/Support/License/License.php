<?php

namespace Spatie\Mailcoach\Domain\Shared\Support\License;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Throwable;

class License
{
    const STATUS_NOT_FOUND = 'not found';
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_INVALID = 'invalid';
    const STATUS_UNKNOWN = 'unknown';

    protected string $cacheKey = 'mailcoach-license-status';

    public function hasExpired(): bool
    {
        return $this->getStatus() === self::STATUS_EXPIRED;
    }

    public function clearCache(): self
    {
        Cache::forget($this->cacheKey);

        return $this;
    }

    public function getStatus(): string
    {
        return Cache::remember(
            $this->cacheKey,
            (int)CarbonInterval::week()->totalSeconds,
            function () {
                try {
                    $licenseKey = $this->licenseKey();
                    if (! $licenseKey) {
                        return self::STATUS_NOT_FOUND;
                    }

                    $response = Http::asJson()
                        ->get("https://spatie.be/api/license/{$licenseKey}");

                    if ($response->status() === 404) {
                        return self::STATUS_INVALID;
                    }

                    $licenseProperties = $response->json();

                    $active = Carbon::createFromTimestamp($licenseProperties['expires_at'])->isFuture();

                    return $active
                        ? self::STATUS_ACTIVE
                        : self::STATUS_EXPIRED;
                } catch (Throwable) {
                    return self::STATUS_UNKNOWN;
                }
            }
        );
    }

    protected function licenseKey(): ?string
    {
        $composerPath = (new ExecutableFinder())->find('composer');

        $process = Process::fromShellCommandline("{$composerPath} config --list | grep http-basic.satis.spatie.be.password");

        $process->run();

        if (! $process->isSuccessful()) {
            return null;
        }

        $output = $process->getOutput();

        if (! str_contains($output, '[http-basic.satis.spatie.be.password] ')) {
            return null;
        }

        $licenseKey = Str::after($output, '[http-basic.satis.spatie.be.password] ');

        return trim($licenseKey);
    }
}
