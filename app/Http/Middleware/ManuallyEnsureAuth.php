<?php

namespace App\Http\Middleware;

use App\AuthUtils;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ManuallyEnsureAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Middleware that gates pages behind a login screen if the user is not authenticated
        // This is an alternative to Laravels 'auth' middleware, that uses DB calls
        if (!AuthUtils::check($request)) {
            return redirect('login');
        }

        return $next($request);
    }
}
