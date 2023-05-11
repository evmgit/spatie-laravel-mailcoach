<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire;

class AutomationActionComponent extends AutomationComponent
{
    public array $action;

    public string $uuid;

    public bool $editing = false;

    public bool $editable = true;

    public bool $deletable = true;

    public int $index = 0;

    public function rules(): array
    {
        return [];
    }

    public function edit()
    {
        $this->editing = true;

        $this->emitUp('editAction', $this->uuid);
    }

    public function save()
    {
        if (! empty($this->rules())) {
            $this->validate();
        }

        $this->emitUp('actionSaved', $this->uuid, $this->getData());

        $this->editing = false;
    }

    public function delete()
    {
        $this->emitUp('actionDeleted', $this->uuid);
    }

    public function getData(): array
    {
        return [];
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.automationAction');
    }
}
