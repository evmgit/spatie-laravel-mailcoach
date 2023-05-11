<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailOpen;

class TransactionalMailOpenFactory extends Factory
{
    protected $model = TransactionalMailOpen::class;

    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid,
            'send_id' => Send::factory(),
        ];
    }
}
