<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;

class AutomationFactory extends Factory
{
    protected $model = Automation::class;

    public function definition()
    {
        return [
            'email_list_id' => EmailList::factory(),
            'name' => $this->faker->sentence,
            'interval' => '1 minute',
            'status' => AutomationStatus::PAUSED,
        ];
    }
}
