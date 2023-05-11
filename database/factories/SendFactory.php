<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SendFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return static::getSendClass();
    }

    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid,
            'campaign_id' => Campaign::factory(),
            'automation_mail_id' => AutomationMail::factory(),
            'subscriber_id' => Subscriber::factory(),
        ];
    }
}
