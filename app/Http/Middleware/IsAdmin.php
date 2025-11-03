<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Por favor faça login para acessar a área administrativa.');
        }

        $user = Auth::user();

        // Check if user is admin (adjust this logic based on your user model)
        // Option 1: Check for is_admin flag
        if (isset($user->is_admin) && $user->is_admin) {
            return $next($request);
        }

        // Option 2: Check for admin role
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return $next($request);
        }

        // Option 3: Check for specific role_id
        if (isset($user->role_id) && $user->role_id == 1) {
            return $next($request);
        }

        // Option 4: Check for admin email (fallback for development)
        if (in_array($user->email, config('app.admin_emails', []))) {
            return $next($request);
        }

        // If none of the conditions match, deny access
        abort(403, 'Acesso não autorizado. Apenas administradores podem acessar esta área.');
    }
}
