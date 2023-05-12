<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;

class AutomationMailFactory extends Factory
{
    protected $model = AutomationMail::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'subject' => $this->faker->sentence,
            'from_email' => $this->faker->email,
            'from_name' => $this->faker->name,
            'html' => $this->faker->randomHtml(),
            'track_opens' => $this->faker->boolean,
            'track_clicks' => $this->faker->boolean,
            'uuid' => $this->faker->uuid,
            'last_modified_at' => now(),
        ];
    }
}
