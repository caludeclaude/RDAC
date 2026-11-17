<?php

namespace App\Http\Middleware;

use App\Support\Audit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    // Used like:  ->middleware('role:super_admin')   or   ->middleware('role:penro,cenro')
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role->value, $roles, true)) {
            Audit::record('DENIED: tried to open /' . $request->path() . ' without permission.', $user);
            abort(403, 'You do not have permission to open this page.');
        }

        return $next($request);
    }
}