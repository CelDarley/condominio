<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'login',
        'auth/login',
        'presenca/*',  // Excluir todas as rotas de presença para permitir JavaScript
        'api/*',       // Excluir APIs se houver
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, \Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            // Log informações de debug para ajudar a diagnosticar o problema
            \Log::error('CSRF Token Mismatch Debug Info:', [
                'url' => $request->url(),
                'method' => $request->method(),
                'session_token' => $request->session()->token(),
                'request_token' => $request->input('_token'),
                'header_token' => $request->header('X-CSRF-TOKEN'),
                'session_id' => $request->session()->getId(),
                'session_started' => $request->session()->isStarted(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
            ]);
            
            // Para requisições AJAX, retornar JSON ao invés de página de erro
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sessão expirada. Por favor, recarregue a página.',
                    'code' => 419
                ], 419);
            }
            
            // Para outras requisições, redirecionar para login com mensagem
            if ($request->method() === 'POST') {
                return redirect()->route('login')->with('error', 'Sessão expirada. Faça login novamente.');
            }
            
            throw $e;
        }
    }
} 