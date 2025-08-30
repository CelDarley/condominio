<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Morador;
use App\Models\Usuario;

class MoradorController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        // Log para debug
        \Log::info('Tentativa de login no app-morador', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent')
        ]);
        
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        // Buscar usuário do tipo 'morador' na tabela usuario centralizada
        $usuario = Usuario::where('email', $request->email)
                         ->where('tipo', 'morador')
                         ->where('ativo', true)
                         ->first();
        
        \Log::info('Resultado da busca de usuário', [
            'email' => $request->email,
            'usuario_encontrado' => $usuario ? 'sim' : 'não',
            'usuario_id' => $usuario ? $usuario->id : null
        ]);
        
        if ($usuario && Hash::check($request->password, $usuario->senha_hash)) {
            // Login bem-sucedido usando o guard morador
            Auth::guard('morador')->login($usuario, $request->filled('remember'));
            $request->session()->regenerate();
            
            \Log::info('Login bem-sucedido', [
                'usuario_id' => $usuario->id,
                'email' => $usuario->email
            ]);
            
            return redirect()->intended(route('dashboard'));
        }
        
        \Log::warning('Falha no login', [
            'email' => $request->email,
            'usuario_existe' => $usuario ? 'sim' : 'não',
            'senha_confere' => $usuario ? (Hash::check($request->password, $usuario->senha_hash) ? 'sim' : 'não') : 'n/a'
        ]);
        
        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros ou sua conta não está ativa.',
        ])->onlyInput('email');
    }
    
    public function showRegistrationForm()
    {
        return view('auth.register');
    }
    
    public function register(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:moradores',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'required|string|max:255',
            'apartamento' => 'required|string|max:10',
            'bloco' => 'nullable|string|max:10',
            'cpf' => 'required|string|unique:moradores|max:14',
            'password' => 'required|string|min:6|confirmed'
        ]);
        
        $morador = Morador::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'endereco' => $request->endereco,
            'apartamento' => $request->apartamento,
            'bloco' => $request->bloco,
            'cpf' => $request->cpf,
            'password' => Hash::make($request->password),
            'ativo' => false // Precisa ser ativado pelo admin
        ]);
        
        return redirect()->route('login')->with('success', 'Conta criada! Aguarde a aprovação do administrador.');
    }
    
    public function logout(Request $request)
    {
        Auth::guard('morador')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home');
    }
}
