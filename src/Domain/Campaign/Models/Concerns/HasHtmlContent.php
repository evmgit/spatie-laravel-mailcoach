<?php

namespace Spatie\Mailcoach\Domain\Campaign\Models\Concerns;

interface HasHtmlContent
{
    public function getHtml(): ?string;

    public function getStructuredHtml(): ?string;
}
