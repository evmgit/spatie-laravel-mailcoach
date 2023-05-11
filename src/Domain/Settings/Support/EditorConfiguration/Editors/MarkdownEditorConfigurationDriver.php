<?php

namespace Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors;

use Spatie\MailcoachMarkdownEditor\Editor;

class MarkdownEditorConfigurationDriver extends EditorConfigurationDriver
{
    public static function label(): string
    {
        return 'Markdown';
    }

    public function getClass(): string
    {
        return Editor::class;
    }

    public static function settingsPartial(): ?string
    {
        return 'mailcoach::app.configuration.editor.partials.markdown';
    }
}
