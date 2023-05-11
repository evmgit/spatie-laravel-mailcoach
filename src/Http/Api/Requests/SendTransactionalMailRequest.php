<?php

namespace Spatie\Mailcoach\Http\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Settings\Rules\MailerConfigKeyNameRule;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Support\AddressNormalizer;
use Spatie\ValidationRules\Rules\Delimited;

class SendTransactionalMailRequest extends FormRequest
{
    use UsesMailcoachModels;

    public function rules(): array
    {
        return [
            'mail_name' => ['string', Rule::exists(self::getTransactionalMailTableName(), 'name')],
            'subject' => ['required', 'string'],
            'html' => ['string'],
            'replacements' => ['array'],
            'from' => ['required'],
            'to' => ['required', (new Delimited('email'))->min(1)],
            'cc' => ['nullable', (new Delimited('email'))->min(1)],
            'bcc' => ['nullable', (new Delimited('email'))->min(1)],
            'reply_to' => ['nullable', (new Delimited('email'))->min(1)],
            'store' => ['boolean'],
            'mailer' => ['string', new MailerConfigKeyNameRule()],
            'attachments' => ['array', 'nullable'],
            'attachments.*.name' => ['required', 'string'],
            'attachments.*.content' => ['required', 'string'],
            'attachments.*.content_type' => ['required', 'string'],
            'attachments.*.content_id' => ['nullable', 'string'],
        ];
    }

    public function replacements(): array
    {
        return $this->get('replacements', []);
    }

    public function attachments(): array
    {
        return $this->get('attachments', []);
    }

    public function shouldStoreMail(): bool
    {
        if (! $this->has('store')) {
            return true;
        }

        return (bool) $this->store;
    }

    public function getFromEmail(): ?string
    {
        $address = (new AddressNormalizer())->normalize($this->from)[0] ?? null;

        if (! $address) {
            return null;
        }

        return $address->getAddress();
    }
}
