<?php

namespace Spatie\Mailcoach\Domain\Campaign\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Spatie\SchemalessAttributes\SchemalessAttributes;

trait HasExtraAttributes
{
    public function getExtraAttributesAttribute(): SchemalessAttributes
    {
        return SchemalessAttributes::createForModel($this, 'extra_attributes');
    }

    public function scopeWithExtraAttributes(): Builder
    {
        /**
         * @psalm-suppress UndefinedMethod
         */
        return $this->extra_attributes->modelScope();
    }
}
