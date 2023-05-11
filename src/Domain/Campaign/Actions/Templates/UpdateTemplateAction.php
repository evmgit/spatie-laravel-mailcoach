<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions\Templates;

use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class UpdateTemplateAction
{
    use UsesMailcoachModels;

    public function execute(Template $template, array $attributes): Template
    {
        $template->update([
            'name' => $attributes['name'],
            'html' => $attributes['html'] ?? '',
            'structured_html' => $attributes['structured_html'] ?? null,
        ]);

        return $template->refresh();
    }
}
