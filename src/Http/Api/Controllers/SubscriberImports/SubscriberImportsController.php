<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\SubscriberImportRequest;
use Spatie\Mailcoach\Http\Api\Resources\SubscriberImportResource;

class SubscriberImportsController
{
    use AuthorizesRequests;
    use UsesMailcoachModels;
    use RespondsToApiRequests;

    public function index()
    {
        $this->authorize("viewAny", self::getEmailListClass());

        $subscribersImport = SubscriberImport::query()->paginate();

        return SubscriberImportResource::collection($subscribersImport);
    }

    public function show(SubscriberImport $subscriberImport)
    {
        $this->authorize("view", $subscriberImport->emailList);

        return new SubscriberImportResource($subscriberImport);
    }

    public function store(SubscriberImportRequest $request)
    {
        $this->authorize("update", self::getEmailListClass()::findOrFail($request->email_list_id));

        $attributes = array_merge($request->validated(), ['status' => SubscriberImportStatus::DRAFT]);

        $subscriberImport = SubscriberImport::create($attributes);

        return new SubscriberImportResource($subscriberImport);
    }

    public function update(SubscriberImportRequest $request, SubscriberImport $subscriberImport)
    {
        $this->authorize("update", $subscriberImport->emailList);

        if ($subscriberImport->status !== SubscriberImportStatus::DRAFT) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Cannot update a non-draft import.');
        }

        $subscriberImport->update($request->validated());

        return new SubscriberImportResource($subscriberImport);
    }

    public function destroy(SubscriberImport $subscriberImport)
    {
        $this->authorize("update", $subscriberImport->emailList);

        $subscriberImport->delete();

        return $this->respondOk();
    }
}
