<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class EnsurePetshopSetup
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Considera o petshop resolvido (admin = dono; colaborador = vínculo).
        if ($user && !$user->currentPetshop()) {
            // Só redireciona se existir um fluxo de setup; caso contrário, deixa passar
            // (evita erro "Route [petshop.setup] not defined").
            if (Route::has('petshop.setup')
                && !$request->routeIs('petshop.setup*')
                && !$request->routeIs('logout')) {
                return redirect()->route('petshop.setup');
            }
        }

        return $next($request);
    }
}
