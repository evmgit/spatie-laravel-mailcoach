<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignClick;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink;

class CampaignClickFactory extends Factory
{
    protected $model = CampaignClick::class;

    public function definition()
    {
        return [
            'send_id' => SendFactory::new(),
            'campaign_link_id' => CampaignLink::factory(),
            'subscriber_id' => Subscriber::factory(),
        ];
    }
}
