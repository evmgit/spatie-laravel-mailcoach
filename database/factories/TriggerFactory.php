<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Automation\Models\Trigger;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;

class TriggerFactory extends Factory
{
    protected $model = Trigger::class;

    public function definition()
    {
        return [
            'uuid' => Str::uuid()->toString(),
            'trigger' => new SubscribedTrigger(),
        ];
    }
}
