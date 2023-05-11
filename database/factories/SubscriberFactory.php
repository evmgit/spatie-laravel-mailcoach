<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SubscriberFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return static::getSubscriberClass();
    }

    public function unconfirmed()
    {
        return $this->state(
            fn (array $attributes) => [
                'subscribed_at' => null,
            ]
        );
    }

    public function definition()
    {
        return [
            'email' => $this->faker->email,
            'uuid' => $this->faker->uuid,
            'subscribed_at' => now(),
            'email_list_id' => EmailList::factory(),
        ];
    }
}
