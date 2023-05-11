<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Http\Api\Requests\AppendSubscriberImportRequest;
use Spatie\Mailcoach\Http\Api\Resources\SubscriberImportResource;

class AppendSubscriberImportController
{
    use AuthorizesRequests;

    public function __invoke(AppendSubscriberImportRequest $request, SubscriberImport $subscriberImport)
    {
        $this->authorize('update', $subscriberImport->emailList);

        $subscriberImport->update([
            'subscribers_csv' => $subscriberImport->subscribers_csv.PHP_EOL.$request->subscribers_csv,
        ]);

        return new SubscriberImportResource($subscriberImport);
    }
}
