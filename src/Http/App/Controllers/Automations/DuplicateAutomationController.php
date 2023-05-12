<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations;

use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class DuplicateAutomationController
{
    use UsesMailcoachModels;

    public function __invoke(Automation $automation)
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Models\Automation $duplicateAutomation */
        $duplicateAutomation = $this->getAutomationClass()::create([
            'name' => __('Duplicate of') . ' ' . $automation->name,
        ]);

        $automation->actions->each(function (Action $action) use ($duplicateAutomation) {
            $actionClass = static::getAutomationActionClass();
            $newAction = $duplicateAutomation->actions()->save($actionClass::make([
                'action' => $action->action->duplicate(),
                'key' => $action->key,
                'order' => $action->order,
            ]));

            foreach ($action->children as $child) {
                $duplicateAutomation->actions()->save($actionClass::make([
                    'parent_id' => $newAction->id,
                    'action' => $child->action->duplicate(),
                    'key' => $child->key,
                    'order' => $child->order,
                ]));
            }
        });

        flash()->success(__('Automation :automation was duplicated.', ['automation' => $automation->name]));

        return redirect()->route('mailcoach.automations.settings', $duplicateAutomation);
    }
}
