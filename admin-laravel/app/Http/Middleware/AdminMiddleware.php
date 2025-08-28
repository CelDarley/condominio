<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'Você precisa estar logado para acessar esta área.');
        }

        $user = Auth::user();
        
        if (!$user->isAdmin() || !$user->ativo) {
            Auth::logout();
            return redirect()->route('admin.login')->with('error', 'Acesso negado. Apenas administradores ativos podem acessar esta área.');
        }

        return $next($request);
    }
}
