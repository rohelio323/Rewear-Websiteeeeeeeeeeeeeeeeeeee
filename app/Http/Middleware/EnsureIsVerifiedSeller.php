<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsVerifiedSeller
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() && auth()->user()->is_verified_seller) {
            return $next($request);
        }

        return redirect()->route('marketplace.index')
            ->with('error', 'Access denied. You must be a verified seller to list items.');
    }
}
