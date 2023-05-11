<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Shared\Actions\CalculateStatisticsAction;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Spatie\Mailcoach\Mailcoach;

class CalculateStatisticsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public Sendable $sendable;

    public $queue;

    public int $uniqueFor = 60;

    public function __construct(Sendable $sendable)
    {
        $this->sendable = $sendable;

        $this->queue = config('mailcoach.shared.perform_on_queue.calculate_statistics_job');

        $this->connection = $this->connection ?? Mailcoach::getQueueConnection();
    }

    public function uniqueId()
    {
        return $this->sendable->uuid;
    }

    public function handle()
    {
        try {
            /** @var \Spatie\Mailcoach\Domain\Shared\Actions\CalculateStatisticsAction $calculateStatistics */
            $calculateStatistics = Mailcoach::getSharedActionClass('calculate_statistics', CalculateStatisticsAction::class);

            $calculateStatistics->execute($this->sendable);
        } catch (Exception $exception) {
            report($exception);
        }
    }
}
