<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            if (
                $user->last_activity_at === null ||
                $user->last_activity_at->lte(now()->subSeconds(30))
            ) {
                $user->forceFill([
                    'last_activity_at' => now(),
                ])->saveQuietly();
            }
        }

        return $next($request);
    }
}
