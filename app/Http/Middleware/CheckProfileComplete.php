<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckProfileComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Jika user sudah login tapi data penting masih kosong
        if ($user && (empty($user->nip) || empty($user->no_hp) || empty($user->departemen_uuid))) {
            // Izinkan akses jika tujuannya memang ke halaman lengkapi data atau logout
            if (!$request->is('lengkapi-data*') && !$request->is('logout')) {
                return redirect()->route('pendaftaran.lengkapi')->with('warning', 'Silahkan lengkapi data profil Anda terlebih dahulu.');
            }
        }

        return $next($request);
    }
}
