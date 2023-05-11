<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class AutomationFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return static::getAutomationClass();
    }

    public function definition()
    {
        return [
            'email_list_id' => EmailList::factory(),
            'name' => $this->faker->sentence,
            'interval' => '1 minute',
            'status' => AutomationStatus::Paused,
        ];
    }
}
