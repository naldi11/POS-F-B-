<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        $userRole = auth()->user()->role;
        
        // Let admin have access to admin specific roles (like promo)
        if ($userRole === 'admin' && in_array('admin', $roles)) {
            return $next($request);
        }
        
        // But if the route is for cashier, admin shouldn't have access if we strictly separate them
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized access. Role required: ' . implode(', ', $roles));
    }
}
