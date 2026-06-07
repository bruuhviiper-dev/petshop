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
        $petshop = $request->user()?->currentPetshop();
        if ($petshop) {
            config(['petshop.current' => $petshop]);
            View::share('currentPetshop', $petshop);
        }

        return $next($request);
    }
}
