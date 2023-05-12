<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Actions;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\File;
use Illuminate\View\Factory;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class RenderTemplateAction
{
    public function execute(TransactionalMailTemplate $template, Mailable $mailable)
    {
        $body = $this->renderTemplateBody($template, $mailable);

        $body = $this->executeReplacers($body, $template, $mailable);

        return $body;
    }

    protected function renderTemplateBody(TransactionalMailTemplate $template, Mailable $mailable): string
    {
        return match ($template->type) {
            'blade' => $this->compileBlade($template->body, $mailable->buildViewData()),
            'markdown' => Markdown::parse($template->body),
            'blade-markdown' => $this->compileBlade(
                bladeString: $template->body,
                data: $mailable->buildViewData(),
                markdown: true,
                theme: $mailable->theme
            ),

            default => $template->body,
        };
    }

    protected function executeReplacers(string $body, TransactionalMailTemplate $template, Mailable $mailable): string
    {
        foreach ($template->replacers() as $replacer) {
            $body = $replacer->replace($body, $mailable, $template);
        }

        return $body;
    }

    protected function compileBlade(string $bladeString, array $data, bool $markdown = false, string $theme = null): string
    {
        $tempDir = new TemporaryDirectory();
        $tempDir->create();
        $path = $tempDir->path('temporary-template-view.blade.php');

        File::put($path, $bladeString);

        $viewFactory = app(Factory::class);
        $viewFactory->addLocation($tempDir->path());
        $viewFactory->flushFinderCache();

        if ($markdown) {
            $html = app(Markdown::class)
                ->theme($theme ?? 'default')
                ->render('temporary-template-view', $data)
                ->toHtml();
        } else {
            $html = $viewFactory->make('temporary-template-view', $data)->render();
        }

        $tempDir->delete();

        return $html;
    }
}
