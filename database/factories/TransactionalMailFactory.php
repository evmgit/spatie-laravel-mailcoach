<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\LaravelRay\Tests\TestClasses\TestMailable;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

class TransactionalMailFactory extends Factory
{
    protected $model = TransactionalMail::class;

    public function definition()
    {
        return [
            'subject' => $this->faker->sentence,
            'body' => $this->faker->randomHtml(),
            'from' => [$this->person()],
            'to' => [$this->person()],
            'cc' => [$this->person()],
            'bcc' => [$this->person()],
            'mailable_class' => TestMailable::class,
            'track_opens' => true,
            'track_clicks' => true,
        ];
    }

    protected function person(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];
    }
}
