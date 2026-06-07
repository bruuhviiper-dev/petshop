<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware que adiciona o header Content-Security-Policy nas respostas web.
 */
class ContentSecurityPolicy
{
    /**
     * Aplica o CSP na resposta HTTP.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set(
            'Content-Security-Policy',
            implode('; ', [
                "default-src 'self'",
                // 'unsafe-eval' é necessário para o Alpine.js v3 avaliar expressões (x-data, @click, :class)
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.jsdelivr.net unpkg.com",
                "style-src 'self' 'unsafe-inline' fonts.bunny.net",
                "font-src 'self' fonts.bunny.net",
                "img-src 'self' data: placedog.net storage.googleapis.com",
                "connect-src 'self'",
                "frame-ancestors 'none'",
                "base-uri 'self'",
                "form-action 'self'",
            ])
        );

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Páginas autenticadas não podem ficar em cache do navegador — senão o
        // botão "voltar" mostra dados desatualizados (ex.: nome antigo na sidebar).
        if ($request->user()) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
        }

        return $response;
    }
}
