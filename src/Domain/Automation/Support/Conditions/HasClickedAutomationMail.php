<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Conditions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Actions\AddUtmTagsToUrlAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class HasClickedAutomationMail implements Condition
{
    use UsesMailcoachModels;

    public function __construct(
        private Automation $automation,
        private Subscriber $subscriber,
        private array $data,
    ) {
    }

    public static function getName(): string
    {
        return (string) __mc('Has clicked automation mail');
    }

    public static function getDescription(array $data): string
    {
        if (! isset($data['automation_mail_id']) || ! $data['automation_mail_id']) {
            return '';
        }

        $mail = static::getAutomationMailClass()::find($data['automation_mail_id']);

        return (string) __mc(':mail - :url', [
            'mail' => $mail->name,
            'url' => isset($data['automation_mail_link_url']) && $data['automation_mail_link_url']
                ? $data['automation_mail_link_url']
                : __mc('Any link'),
        ]);
    }

    public static function rules(): array
    {
        return [
            'automation_mail_id' => [
                'required',
                Rule::exists(self::getAutomationMailTableName(), 'id'),
            ],
            'automation_mail_link_url' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function check(): bool
    {
        $query = static::getAutomationMailClickClass()::query()
            ->where('subscriber_id', $this->subscriber->id)
            ->whereHas('send', function (Builder $query) {
                $query->where('automation_mail_id', $this->data['automation_mail_id']);
            });

        if ($this->data['automation_mail_link_url'] ?? false) {
            $mail = static::getAutomationMailClass()::find($this->data['automation_mail_id']);
            $url = $this->data['automation_mail_link_url'];

            if ($mail->utm_tags) {
                $url = app(AddUtmTagsToUrlAction::class)->execute($url, $mail->name);
            }

            $query->whereHas('link', function (Builder $query) use ($url) {
                $query->where('url', $url);
            });
        }

        return $query->exists();
    }
}
