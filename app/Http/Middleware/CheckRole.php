<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user is blocked
        if ($user->isBlocked()) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account has been blocked.');
        }

        // Check if user has required role
        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}

