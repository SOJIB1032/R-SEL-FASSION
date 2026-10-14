<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->isAdmin()) {
            abort(403, 'Admin accounts cannot access the customer storefront actions.');
        }
        if ($request->user() && ! $request->user()->is_active) {
            abort(403, 'This account has been deactivated.');
        }

        return $next($request);
    }
}
