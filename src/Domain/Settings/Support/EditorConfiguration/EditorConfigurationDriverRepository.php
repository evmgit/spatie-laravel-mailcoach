<?php

namespace Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration;

use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors\EditorConfigurationDriver;
use Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors\TextareaEditorConfigurationDriver;

class EditorConfigurationDriverRepository
{
    /** @return Collection<EditorConfigurationDriver> */
    public function getSupportedEditors(): Collection
    {
        return collect(config('mailcoach.editors'))
            /** @var class-string<EditorConfigurationDriver> $editorConfigurationDriver */
            ->map(function (string $editorConfigurationDriver) {
                return resolve($editorConfigurationDriver);
            });
    }

    public function getForEditor(string $editorLabel): EditorConfigurationDriver
    {
        $configuredEditor = $this->getSupportedEditors()
            ->first(fn (EditorConfigurationDriver $editor) => $editor->label() === $editorLabel);

        return $configuredEditor ?? app(TextareaEditorConfigurationDriver::class);
    }

    public function getForClass(string $class): EditorConfigurationDriver
    {
        $configuredEditor = $this->getSupportedEditors()
            ->first(fn (EditorConfigurationDriver $editor) => $editor->getClass() === $class);

        return $configuredEditor ?? app(TextareaEditorConfigurationDriver::class);
    }
}
