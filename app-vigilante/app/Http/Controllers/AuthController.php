<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        // Temporariamente usando versão simplificada para contornar erro JavaScript
        return view('auth.login-simple');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'senha' => 'required|string',
        ], [
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'Por favor, insira um email válido.',
            'senha.required' => 'O campo senha é obrigatório.',
        ]);

        $email = $request->input('email');
        $senha = $request->input('senha');

        // Buscar usuário pelo email
        $usuario = Usuario::where('email', $email)->first();

        if (!$usuario) {
            return back()->withErrors([
                'email' => 'Email não encontrado.',
            ])->withInput($request->only('email'));
        }

        // Verificar se é vigilante
        if (!$usuario->isVigilante()) {
            return back()->withErrors([
                'email' => 'Acesso restrito a vigilantes.',
            ])->withInput($request->only('email'));
        }

        // Verificar se está ativo
        if (!$usuario->ativo) {
            return back()->withErrors([
                'email' => 'Usuário inativo. Entre em contato com o administrador.',
            ])->withInput($request->only('email'));
        }

        // Verificar senha
        if (!$usuario->checkPassword($senha)) {
            return back()->withErrors([
                'senha' => 'Senha incorreta.',
            ])->withInput($request->only('email'));
        }

        // Fazer login
        Auth::login($usuario, $request->filled('remember'));

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logout realizado com sucesso.');
    }
} 