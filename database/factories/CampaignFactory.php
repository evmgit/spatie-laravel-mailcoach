<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CampaignFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return static::getCampaignClass();
    }

    public function definition()
    {
        return [
            'subject' => $this->faker->sentence,
            'from_email' => $this->faker->email,
            'from_name' => $this->faker->name,
            'html' => $this->faker->randomHtml(),
            'status' => CampaignStatus::Draft,
            'uuid' => $this->faker->uuid,
            'last_modified_at' => now(),
            'email_list_id' => EmailList::factory(),
            'template_id' => Template::factory(),
        ];
    }
}
