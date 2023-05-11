<?php

namespace Spatie\Mailcoach\Http\Api\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Resources\SendResource;
use Spatie\Mailcoach\Http\App\Queries\SendsQuery;

class SendsController
{
    use AuthorizesRequests;
    use RespondsToApiRequests;
    use UsesMailcoachModels;

    public function index(SendsQuery $sendsQuery)
    {
        $this->authorize('viewAny', static::getSendClass());

        $sends = $sendsQuery->paginate();

        return SendResource::collection($sends);
    }

    public function show(Send $send)
    {
        $this->authorize('view', $send);

        return new SendResource($send);
    }

    public function destroy(Send $send)
    {
        $this->authorize('delete', $send);

        $send->delete();

        return $this->respondOk();
    }
}
