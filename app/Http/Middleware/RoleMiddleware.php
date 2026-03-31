<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // If not logged in at all, send to login page
        if (! Auth::check()) {
            return redirect('/login');
        }

        // ?-> safely returns null instead of crashing if role is not set
        $userRole = Auth::user()->role?->name;

        // If the user has no role, or their role isn't in the allowed list → 403
        if (! $userRole || ! in_array($userRole, $roles)) {
            abort(403, 'Unauthorized. You do not have permission to access this page.');
        }

        return $next($request);
    }
}