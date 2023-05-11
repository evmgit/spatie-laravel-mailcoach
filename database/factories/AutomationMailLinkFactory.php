<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailLink;

class AutomationMailLinkFactory extends Factory
{
    protected $model = AutomationMailLink::class;

    public function definition()
    {
        return [
            'automation_mail_id' => AutomationMail::factory(),
            'url' => $this->faker->url,
        ];
    }
}
