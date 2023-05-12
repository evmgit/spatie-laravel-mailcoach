<?php

namespace Spatie\Mailcoach\Domain\Campaign\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUuid
{
    public static $fakeUuid = null;

    public static function bootHasUuid()
    {
        static::creating(function (Model $model) {
            $model->uuid = static::$fakeUuid ?? $model->uuid ?? (string) Str::uuid();
        });
    }

    public static function findByUuid(string $uuid): ?Model
    {
        return static::where('uuid', $uuid)->first();
    }
}
