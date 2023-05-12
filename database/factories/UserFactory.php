<?php

namespace Spatie\Mailcoach\Database\Factories;

use \Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Auth\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'email' => $this->faker->email,
        ];
    }
}
