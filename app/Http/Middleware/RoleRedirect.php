<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class RoleRedirect
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Avoid redirect loop on dashboard routes
        if ($request->routeIs('dashboard')) {

            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('dispatcher')) {
                return redirect()->route('dispatcher.dashboard');
            }

            if ($user->hasRole('driver')) {
                return redirect()->route('driver.dashboard');
            }

            if ($user->hasRole('patient')) {
                return redirect()->route('patient.dashboard');
            }
        }

        return $next($request);
    }
}