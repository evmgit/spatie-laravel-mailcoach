<?php

namespace Spatie\Mailcoach\Domain\Campaign\Rules;

use Exception;
use Illuminate\Contracts\Validation\Rule;
use Spatie\Mailcoach\Domain\Shared\Actions\CreateDomDocumentFromHtmlAction;

class HtmlRule implements Rule
{
    private ?Exception $exception;

    public function passes($attribute, $value)
    {
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
        return (string)__('The HTML is not valid (:message).', ['message' => $this->exception->getMessage()]);
    }
}
