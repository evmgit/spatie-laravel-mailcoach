<?php

namespace Spatie\Mailcoach\Domain\Campaign\Rules;

use Exception;
use Illuminate\Contracts\Validation\Rule;
use Spatie\Mailcoach\Domain\Shared\Actions\CreateDomDocumentFromHtmlAction;

class HtmlRule implements Rule
{
    private ?Exception $exception;

    private ?string $value;

    public function passes($attribute, $value)
    {
        if (is_array($value)) {
            $value = $value['html'];
        }

        $this->value = $value;

        try {
            app(CreateDomDocumentFromHtmlAction::class)->execute($value, false);

            return true;
        } catch (Exception $exception) {
            $this->exception = $exception;

            return false;
        }
    }

    public function message(): string
    {
        preg_match('/Tag (.*) invalid in Entity.*/', $this->exception->getMessage(), $match);

        if (isset($match[1])) {
            return __mc('Your HTML contains a &lt;:tag&gt; tag which is not supported in a lot of mail clients.', [
                'tag' => $match[1],
            ]);
        }

        preg_match('/line: (.*)/', $this->exception->getMessage(), $match);

        $line = $match[1] ?? null;
        if ($line) {
            $lines = explode("\n", $this->value);

            $code = trim($lines[$line - 1] ?? $lines[$line]);
        }

        $message = str_replace('DOMDocument::loadHTML(): ', '', $this->exception->getMessage());

        if (isset($code)) {
            $code = htmlentities($code);
            $message .= "<pre><code class='markup-code mt-2'>{$code}</code></pre>";
        }

        return ucfirst($message);
    }
}
