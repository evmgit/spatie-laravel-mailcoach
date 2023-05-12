<?php

namespace Spatie\Mailcoach\Components;

use Illuminate\View\Component;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\Mailcoach\Domain\TransactionalMail\Support\Replacers\TransactionalMailReplacer;

class TransactionalMailTemplateReplacerHelpTextsComponent extends Component
{
    public function __construct(
        public TransactionalMailTemplate $template
    ) {
    }

    public function replacerHelpTexts(): array
    {
        return collect($this->template->replacers)
            ->map(fn (string $replacerKeyInConfig) => config("mailcoach.transactional.replacers.{$replacerKeyInConfig}"))
            ->filter()
            ->filter(fn (string $className) => class_exists($className))
            ->map(fn (string $className) => resolve($className))
            ->flatMap(fn (TransactionalMailReplacer $replacer) => $replacer->helpText())
            ->toArray();
    }

    public function render()
    {
        return view('mailcoach::app.components.replacerHelpTexts');
    }
}
