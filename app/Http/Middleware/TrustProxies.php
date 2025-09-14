<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrustProxies
{
    /**
     * Tentukan proxy yang dipercaya. Gunakan '*' agar menerima semua.
     *
     * @var array|string|null
     */
    protected $proxies = '*'; // Atau bisa pakai IP Cloudflare kalau ingin spesifik

    // Pastikan header X-Forwarded-* dibaca semua
    protected $headers =
    Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;

    /**
     * Header yang digunakan untuk mengambil IP.
     *
     * @var int
     */


    public function handle($request, Closure $next)
    {
        if ($request->server->has('HTTP_CF_CONNECTING_IP')) {
            $request->server->set('REMOTE_ADDR', $request->server->get('HTTP_CF_CONNECTING_IP'));
        }

        return $next($request);
    }
}
