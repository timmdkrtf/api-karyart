<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Jika method OPTIONS (preflight), langsung balikin respons kosong
        if ($request->getMethod() === "OPTIONS") {
            return response()->json('OK', 200, $this->getCorsHeaders());
        }

        // Untuk request biasa, lanjutkan dan tambahkan header CORS
        $response = $next($request);

        foreach ($this->getCorsHeaders() as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }

    private function getCorsHeaders()
    {
        return [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Origin, Content-Type, Authorization, X-Requested-With',
        ];
    }
}
