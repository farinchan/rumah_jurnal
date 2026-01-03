<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if user is authenticated and has admin roles
        if ($user && $user->hasRole('super-admin|keuangan|editor|humas')) {
            // Check if 2FA is verified for this session
            if (!session('2fa:verified')) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Silakan login kembali dan verifikasi 2FA.');
            }
        }

        return $next($request);
    }
}
