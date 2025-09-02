<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthVigilante
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado para acessar esta página.');
        }

        $user = Auth::user();
        
        if (!$user->isVigilante()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Acesso restrito a vigilantes.');
        }

        if (!$user->ativo) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Usuário inativo. Entre em contato com o administrador.');
        }

        return $next($request);
    }
} 