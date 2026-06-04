<?php

use App\Http\Middleware\ContentSecurityPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetPetshopContext::class,
            ContentSecurityPolicy::class,
        ]);
        $middleware->alias([
            'petshop.setup' => \App\Http\Middleware\EnsurePetshopSetup::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->booted(function (): void {
        RateLimiter::for('agendamento-publico', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())->response(function () {
                return response()->json(
                    ['error' => 'Muitas tentativas. Aguarde 1 minuto.'],
                    429
                );
            });
        });
    })
    ->create();
