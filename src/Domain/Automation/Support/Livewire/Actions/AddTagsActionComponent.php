<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions;

use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationActionComponent;
use Spatie\ValidationRules\Rules\Delimited;

class AddTagsActionComponent extends AutomationActionComponent
{
    public string $tags = '';

    public function getData(): array
    {
        return [
            'tags' => $this->tags,
        ];
    }

    public function rules(): array
    {
        return [
            'tags' => ['required', new Delimited('string')],
        ];
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.actions.addTagsAction');
    }
}
