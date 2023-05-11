<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Actions;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\View\Factory;
use Spatie\Mailcoach\Domain\Shared\Actions\RenderMarkdownToHtmlAction;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class RenderTemplateAction
{
    public function execute(
        TransactionalMail $template,
        Mailable $mailable,
        array $replacements = [],
    ) {
        $body = $template->body;

        $body = $this->renderTemplateBody($template, $body, $mailable);

        $body = $this->handleReplacements($body, $replacements);

        $body = $this->executeReplacers($body, $template, $mailable);

        return $body;
    }

    protected function renderTemplateBody(
        TransactionalMail $template,
        string $body,
        Mailable $mailable,
    ): string {
        return match ($template->type) {
            'blade' => Blade::render($body, $mailable->buildViewData()),
            'markdown' => (string) app(RenderMarkdownToHtmlAction::class)->execute($body),
            'blade-markdown' => $this->compileBladeMarkdown(
                bladeString: $body,
                data: $mailable->buildViewData(),
                theme: $mailable->theme
            ),

            default => $body,
        };
    }

    protected function handleReplacements(string $body, array $replacements): string
    {
        foreach ($replacements as $search => $replace) {
            $body = str_replace("::{$search}::", $replace, $body);
        }

        return $body;
    }

    protected function executeReplacers(string $body, TransactionalMail $template, Mailable $mailable): string
    {
        foreach ($template->replacers() as $replacer) {
            $body = $replacer->replace($body, $mailable, $template);
        }

        return $body;
    }

    protected function compileBladeMarkdown(string $bladeString, array $data, string $theme = null): string
    {
        $tempDir = (new TemporaryDirectory())->create();
        $path = $tempDir->path('temporary-template-view.blade.php');

        File::put($path, $bladeString);

        $viewFactory = app(Factory::class);
        $viewFactory->addLocation($tempDir->path());

        $html = app(Markdown::class)
            ->theme($theme ?? 'default')
            ->render('temporary-template-view', $data)
            ->toHtml();

        $tempDir->delete();

        return $html;
    }
}
