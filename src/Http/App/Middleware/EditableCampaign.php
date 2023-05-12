<?php

namespace Spatie\Mailcoach\Http\App\Middleware;

use Illuminate\Http\Request;

class EditableCampaign
{
    public function handle(Request $request, $next)
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign|null $campaign */
        if (! $campaign = $request->route()->parameter('campaign')) {
            return $next($request);
        }

        return $campaign->isEditable()
            ? $next($request)
            : redirect()->route('mailcoach.campaigns.summary', $campaign);
    }
}
