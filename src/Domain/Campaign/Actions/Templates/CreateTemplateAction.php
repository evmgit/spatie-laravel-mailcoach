<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions\Templates;

use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateTemplateAction
{
    use UsesMailcoachModels;

    public function execute(array $attributes): Template
    {
        return $this->getTemplateClass()::create([
            'name' => $attributes['name'],
            'html' => $attributes['html'] ?? '',
            'structured_html' => $attributes['structured_html'] ?? null,
        ]);
    }
}
