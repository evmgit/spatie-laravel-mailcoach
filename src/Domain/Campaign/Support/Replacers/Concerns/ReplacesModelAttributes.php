<?php

namespace Spatie\Mailcoach\Domain\Campaign\Support\Replacers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Spatie\SchemalessAttributes\SchemalessAttributes;

trait ReplacesModelAttributes
{
    public function replaceModelAttributes(string $text, string $replaceText, Model $model)
    {
        return preg_replace_callback('/(::|%3A%3A)'.$replaceText.'\.([\w.]+)(::|%3A%3A)/', function (array $match) use ($model) {
            $parts = collect(explode('.', $match[2] ?? ''));

            $replace = $parts->reduce(function ($value, $part) {
                if ($value instanceof SchemalessAttributes) {
                    return $value->get($part) ?? '';
                }

                return $value->$part
                        ?? $value[$part]
                        ?? '';
            }, $model);

            return $replace ?? $match;
        }, $text);
    }
}
