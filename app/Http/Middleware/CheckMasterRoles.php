<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckMasterRoles
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            abort(403);
        }

        if (!$request->user()->hasAnyRole(['admin', 'injection_director'])) {
            abort(403);
        }

        return $next($request);
    }
}
