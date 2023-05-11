<?php

namespace Spatie\Mailcoach\Domain\Campaign\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Mailcoach;

class CreateCampaignSendJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    protected Campaign $campaign;

    protected Subscriber $subscriber;

    public $tries = 1;

    public $uniqueFor = 45;

    /** @var string */
    public $queue;

    public function uniqueId(): string
    {
        return "{$this->campaign->id}-{$this->subscriber->id}";
    }

    public function __construct(Campaign $campaign, Subscriber $subscriber)
    {
        $this->campaign = $campaign;
        $this->subscriber = $subscriber;

        $this->queue = config('mailcoach.campaigns.perform_on_queue.send_campaign_job');

        $this->connection = $this->connection ?? Mailcoach::getQueueConnection();
    }

    public function handle()
    {
        if ($this->campaign->isCancelled()) {
            return;
        }

        $pendingSend = $this->campaign->sends()
            ->where('subscriber_id', $this->subscriber->id)
            ->exists();

        if ($pendingSend) {
            return;
        }

        $this->campaign->sends()->create([
            'subscriber_id' => $this->subscriber->id,
            'uuid' => (string) Str::uuid(),
        ]);
    }
}
