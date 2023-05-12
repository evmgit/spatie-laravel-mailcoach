<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;

class SubscriberImportFactory extends Factory
{
    protected $model = SubscriberImport::class;

    public function definition()
    {
        return [
            'status' => SubscriberImportStatus::COMPLETED,
            'email_list_id' => EmailList::factory(),
            'imported_subscribers_count' => $this->faker->numberBetween(1, 1000),
            'error_count' => $this->faker->numberBetween(1, 1000),
        ];
    }
}
