<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_admin) {
            if ($request->expectsJson() || $request->is('api/*')) {
                abort(403, 'No autorizado.');
            }

            return redirect()->route('home');
        }

        return $next($request);
    }
}
