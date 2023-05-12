<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailClick;

class TransactionalMailClickFactory extends Factory
{
    protected $model = TransactionalMailClick::class;

    public function definition()
    {
        return [
            'send_id' => Send::factory(),
            'url' => $this->faker->url,
        ];
    }
}
