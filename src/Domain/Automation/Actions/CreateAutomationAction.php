<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateAutomationAction
{
    use UsesMailcoachModels;

    public function execute(array $attributes): Automation
    {
        return $this->getAutomationClass()::create([
            'name' => $attributes['name'],
            'email_list_id' => $attributes['email_list_id'] ?? null,
            'interval' => '10 minutes',
        ]);
    }
}
