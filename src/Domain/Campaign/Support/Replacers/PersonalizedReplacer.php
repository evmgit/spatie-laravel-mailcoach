<?php

namespace Spatie\Mailcoach\Domain\Campaign\Support\Replacers;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

interface PersonalizedReplacer extends ReplacerWithHelpText
{
    public function replace(string $text, Send $pendingSend): string;
}
