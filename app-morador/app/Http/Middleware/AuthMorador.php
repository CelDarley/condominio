<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthMorador
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('morador')->check()) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado para acessar esta página.');
        }

        // Verificar se o morador está ativo
        $morador = Auth::guard('morador')->user();
        if (!$morador->ativo) {
            Auth::guard('morador')->logout();
            return redirect()->route('login')->with('error', 'Sua conta foi desativada. Entre em contato com a administração.');
        }

        return $next($request);
    }
}
