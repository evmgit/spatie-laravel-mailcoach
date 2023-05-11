<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Settings\Enums\MailerTransport;
use Spatie\Mailcoach\Domain\Settings\Models\Mailer;

class MailerFactory extends Factory
{
    protected $model = Mailer::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'transport' => MailerTransport::Ses,
        ];
    }
}
