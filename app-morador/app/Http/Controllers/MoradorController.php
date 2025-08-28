<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Morador;

class MoradorController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        // Tentar autenticar usando a guard padrão com moradores
        $credentials = $request->only('email', 'password');
        $credentials['ativo'] = true; // Só permite login de moradores ativos
        
        if (Auth::guard('morador')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }
        
        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
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
