<?php

namespace Spatie\Mailcoach\Http\App\Middleware;

use Spatie\Flash\Flash;

class SetMailcoachDefaults
{
    public function handle($request, $next)
    {
        Flash::levels([
            'success' => 'success',
            'warning' => 'warning',
            'error' => 'error',
        ]);

        return $next($request);
    }
}
