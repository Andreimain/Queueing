<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsGuard
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isGuard()) {
            abort(403);
        }

        return $next($request);
    }
}
