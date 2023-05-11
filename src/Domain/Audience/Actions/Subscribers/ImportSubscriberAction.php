<?php

namespace Spatie\Mailcoach\Domain\Audience\Actions\Subscribers;

use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Domain\Audience\Support\ImportSubscriberRow;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class ImportSubscriberAction
{
    use UsesMailcoachModels;

    protected array $values;

    public function execute(SubscriberImport $subscriberImport, array $values)
    {
        $subscriberImport->increment('imported_subscribers_count');

        $row = new ImportSubscriberRow($subscriberImport->emailList, $values);

        if (self::getSubscriberClass()::query()
            ->where('email_list_id', $subscriberImport->emailList->id)
            ->where('email', $row->getEmail())
            ->where('imported_via_import_uuid', $subscriberImport->uuid)
            ->exists()
        ) {
            return;
        }

        if (! $row->hasValidEmail()) {
            $subscriberImport->addError(__mc('Does not have a valid email'), $row);

            return;
        }

        if (! $subscriberImport->subscribe_unsubscribed && $row->hasUnsubscribed()) {
            $subscriberImport->addError(__mc('This email address was unsubscribed in the past.'), $row);

            return;
        }

        $attributes = array_merge(
            $row->getAttributes(),
            [
                'extra_attributes' => $row->getExtraAttributes(),
                'imported_via_import_uuid' => $subscriberImport->uuid,
            ],
        );

        self::getSubscriberClass()::createWithEmail($row->getEmail(), $attributes)
            ->skipConfirmation()
            ->tags($row->tags())
            ->replaceTags($subscriberImport->replace_tags)
            ->subscribedAt($row->subscribedAt())
            ->subscribeTo($subscriberImport->emailList);
    }
}
