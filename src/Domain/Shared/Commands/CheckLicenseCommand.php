<?php

namespace Spatie\Mailcoach\Domain\Shared\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Spatie\Mailcoach\Domain\Shared\Support\License\License;

class CheckLicenseCommand extends Command
{
    public $signature = 'mailcoach:check-license';

    public $description = 'Check if the current license is valid';

    protected Carbon $now;

    public function handle()
    {
        $status = (new License())
            ->clearCache()
            ->getStatus();

        if ($status === License::STATUS_ACTIVE) {
            $this->info('✅  Your Mailcoach license is valid.');

            return;
        }

        if ($status === License::STATUS_INVALID) {
            $this->info('❌  Your Mailcoach license is invalid. Make sure you use a the license key displayed at https://spatie.be/products/mailcoach');

            return;
        }

        if ($status === License::STATUS_EXPIRED) {
            $this->info('❌  Your Mailcoach license has expired. Visit https://spatie.be/products/mailcoach to renew your license.');

            return;
        }

        if ($status === License::STATUS_NOT_FOUND) {
            $this->info('❌  No Mailcoach license was found. Visit https://spatie.be/products/mailcoach to purchase a license.');

            return;
        }

        $this->warn('Could not determine the status of your license...');
    }
}
