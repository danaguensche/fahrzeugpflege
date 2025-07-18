<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        Log::info('CheckRole Middleware: User authenticated: ' . ($request->user() ? 'Yes' : 'No'));
        if ($request->user()) {
            Log::info('CheckRole Middleware: User role: ' . $request->user()->role);
            Log::info('CheckRole Middleware: Allowed roles: ' . implode(', ', $roles));
        }

        if (! $request->user() || ! in_array($request->user()->role, $roles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
