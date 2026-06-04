<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetPetshopContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->petshop) {
            $petshop = $request->user()->petshop;
            config(['petshop.current' => $petshop]);
            View::share('currentPetshop', $petshop);
        }

        return $next($request);
    }
}
