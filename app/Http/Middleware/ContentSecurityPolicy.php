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
                "script-src 'self' 'unsafe-inline' cdn.jsdelivr.net unpkg.com",
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

        return $response;
    }
}
