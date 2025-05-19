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

    /**
     * Header yang digunakan untuk mengambil IP.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR;

    public function handle($request, Closure $next)
    {
        if ($request->server->has('HTTP_CF_CONNECTING_IP')) {
            $request->server->set('REMOTE_ADDR', $request->server->get('HTTP_CF_CONNECTING_IP'));
        }

        return $next($request);
    }
}
