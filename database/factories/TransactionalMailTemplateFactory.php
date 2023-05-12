<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\Mailcoach\Tests\TestClasses\TestMailableWithTemplate;

class TransactionalMailTemplateFactory extends Factory
{
    protected $model = TransactionalMailTemplate::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'subject' => $this->faker->sentence,
            'from' => $this->faker->email,
            'to' => [$this->faker->email],
            'cc' => [$this->faker->email],
            'bcc' => [$this->faker->email],
            'body' => $this->faker->randomHtml(),
            'type' => 'blade',
            'track_opens' => true,
            'track_clicks' => true,
            'test_using_mailable' => TestMailableWithTemplate::class,
        ];
    }
}
