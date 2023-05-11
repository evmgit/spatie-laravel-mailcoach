<?php

namespace Spatie\Mailcoach\Domain\Shared\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

trait HasTemplate
{
    use UsesMailcoachModels;

    public function template(): BelongsTo
    {
        return $this->belongsTo(self::getTemplateClass());
    }
}
