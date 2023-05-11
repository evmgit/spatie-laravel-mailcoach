<?php

namespace Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors;

use Spatie\MailcoachEditor\Editor;

class EditorJsEditorConfigurationDriver extends EditorConfigurationDriver
{
    public static function label(): string
    {
        return 'Editor.js';
    }

    public function getClass(): string
    {
        return Editor::class;
    }

    public static function settingsPartial(): ?string
    {
        return 'mailcoach::app.configuration.editor.partials.editor';
    }
}
