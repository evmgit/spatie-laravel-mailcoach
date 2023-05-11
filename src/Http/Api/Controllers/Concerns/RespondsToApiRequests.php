<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Concerns;

use Illuminate\Http\Response;

trait RespondsToApiRequests
{
    public function respondOk()
    {
        return response('', Response::HTTP_NO_CONTENT);
    }
}
