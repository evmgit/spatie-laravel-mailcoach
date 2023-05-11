<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Replacers;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

interface PersonalizedReplacer extends ReplacerWithHelpText
{
    public function replace(string $text, Send $pendingSend): string;
}
