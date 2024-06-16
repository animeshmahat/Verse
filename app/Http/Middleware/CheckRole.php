<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle($request, Closure $next, $role)
    {
        $user = Auth::user();

        if (!$user || ($role == 'superadmin' && $user->role != 'superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
