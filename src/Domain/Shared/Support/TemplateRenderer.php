<?php

namespace Spatie\Mailcoach\Domain\Shared\Support;

class TemplateRenderer
{
    public function __construct(protected string $html)
    {
    }

    public function containsPlaceHolders(): bool
    {
        return count($this->placeHolderNames()) > 0;
    }

    public function placeHolderNames(): array
    {
        preg_match_all('/\[\[\[(.*?)\]\]\]/', $this->html, $matches);

        return array_unique($matches[1]);
    }

    public function fields(): array
    {
        if (! $this->containsPlaceHolders()) {
            return [
                ['name' => 'html', 'type' => 'editor'],
            ];
        }

        return collect($this->placeHolderNames())
            ->map(function (string $name) {
                $parts = explode(':', $name);

                return [
                    'name' => $parts[0],
                    'type' => $parts[1] ?? 'editor',
                ];
            })->toArray();
    }

    public function render(array $values): string
    {
        $html = $this->html;

        if (! $this->containsPlaceHolders()) {
            $html = $values['html'] ?? '';

            if (is_array($html)) {
                return $html['html'] ?? '';
            }

            return $html;
        }

        foreach ($this->fields() as $field) {
            $value = $values[$field['name']] ?? '';

            if (is_array($value)) {
                $value = $value['html'] ?? '';
            }

            $name = $field['name'];
            if ($field['type'] !== 'editor') {
                $name = "{$name}:{$field['type']}";
            }

            $html = str_replace(
                '[[['.$name.']]]',
                $value,
                $html,
            );
        }

        return $html;
    }
}
