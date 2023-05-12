<?php

namespace Spatie\Mailcoach\Domain\Shared\Support\Editor;

use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasHtmlContent;

interface Editor
{
    public function render(HasHtmlContent $model): string;
}
