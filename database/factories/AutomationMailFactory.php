<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class AutomationMailFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return static::getAutomationMailClass();
    }

    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'subject' => $this->faker->sentence,
            'from_email' => $this->faker->email,
            'from_name' => $this->faker->name,
            'html' => $this->faker->randomHtml(),
            'uuid' => $this->faker->uuid,
            'last_modified_at' => now(),
        ];
    }
}
