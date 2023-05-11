<?php

namespace Spatie\Mailcoach\Domain\Campaign\Models\Concerns;

interface HasHtmlContent
{
    public function hasTemplates(): bool;

    public function getHtml(): ?string;

    public function setHtml(string $html): void;

    public function getStructuredHtml(): ?string;

    public function getTemplateFieldValues(): array;

    public function setTemplateFieldValues(array $fieldValues = []): self;
}
