<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class EmailListFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return static::getEmailListClass();
    }

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'uuid' => $this->faker->uuid,
            'default_from_email' => $this->faker->email,
            'default_from_name' => $this->faker->name,
            'default_reply_to_email' => $this->faker->email,
            'default_reply_to_name' => $this->faker->name,
            'campaign_mailer' => config('mail.default'),
            'automation_mailer' => config('mail.default'),
            'transactional_mailer' => config('mail.default'),
        ];
    }
}
