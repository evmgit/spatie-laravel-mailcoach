<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;

class WebhookConfigurationFactory extends Factory
{
    protected $model = WebhookConfiguration::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'url' => $this->faker->url,
            'secret' => $this->faker->word,
            'use_for_all_lists' => true,
        ];
    }
}
