<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePetshopSetup
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && !$request->user()->petshop) {
            if (!$request->routeIs('petshop.setup*') && !$request->routeIs('logout')) {
                return redirect()->route('petshop.setup');
            }
        }

        return $next($request);
    }
}
