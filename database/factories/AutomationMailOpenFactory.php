<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailOpen;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class AutomationMailOpenFactory extends Factory
{
    protected $model = AutomationMailOpen::class;

    public function definition()
    {
        return [
            'send_id' => Send::factory(),
        ];
    }
}
