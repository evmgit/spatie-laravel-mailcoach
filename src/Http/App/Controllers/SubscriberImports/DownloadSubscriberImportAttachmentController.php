<?php

namespace Spatie\Mailcoach\Http\App\Controllers\SubscriberImports;

use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;

class DownloadSubscriberImportAttachmentController
{
    public function __invoke(SubscriberImport $subscriberImport, string $collection)
    {
        abort_unless(! ! $subscriberImport->getMediaCollection($collection), 403);

        return $subscriberImport->getFirstMedia($collection);
    }
}
