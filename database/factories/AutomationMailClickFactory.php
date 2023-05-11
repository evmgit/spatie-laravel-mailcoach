<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailClick;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailLink;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class AutomationMailClickFactory extends Factory
{
    protected $model = AutomationMailClick::class;

    public function definition()
    {
        return [
            'send_id' => Send::factory(),
            'automation_mail_link_id' => AutomationMailLink::factory(),
            'subscriber_id' => Subscriber::factory(),
        ];
    }
}
