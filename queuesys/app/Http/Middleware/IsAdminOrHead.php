<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdminOrHead
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->isHead())) {
            abort(403);
        }

        return $next($request);
    }
}
