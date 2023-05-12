<?php

namespace Spatie\Mailcoach\Http\App\Controllers\SubscriberImports;

use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;

class DestroySubscriberImportController
{
    public function __invoke(SubscriberImport $subscriberImport)
    {
        $subscriberImport->delete();

        flash()->success(__('Import was deleted.'));

        return back();
    }
}
