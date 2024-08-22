<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        $role = explode('|', $role);
        if (!Auth::check()) {
            return redirect('/');
        }
        $user = Auth::user();
        if (!in_array($user->role, $role)) {
            abort(401);
        }
        return $next($request);
    }
}
