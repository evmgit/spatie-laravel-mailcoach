<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Automations;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\Trigger;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\WebhookTrigger;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class TriggerAutomationController
{
    use UsesMailcoachModels;

    public function __invoke(Request $request, Automation $automation)
    {
        $request->validate([
            'subscribers' => ['required', 'array'],
            'subscribers.*' => [Rule::exists(self::getSubscriberTableName(), 'uuid')],
        ]);

        $webhookTriggers = $automation->triggers->filter(function (Trigger $trigger) {
            return $trigger->trigger instanceof WebhookTrigger;
        });

        abort_unless($webhookTriggers->count() > 0, 400, 'This automation does not have a Webhook trigger.');

        $webhookTriggers->each(function (Trigger $trigger) use ($request) {
            $trigger
                ->getAutomationTrigger()
                ->runAutomation(static::getSubscriberClass()::query()->whereIn('uuid', $request->get('subscribers')));
        });

        return response()->json();
    }
}
