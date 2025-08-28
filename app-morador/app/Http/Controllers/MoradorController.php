<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        
        $morador = \App\Models\Morador::where('email', $request->email)
            ->where('ativo', true)
            ->first();
            
        if ($morador && password_verify($request->password, $morador->password)) {
            session(['morador_id' => $morador->id]);
            return redirect()->route('dashboard');
        }
        
        return back()->withErrors(['email' => 'Credenciais inválidas']);
    }
    
    public function showRegistrationForm()
    {
        return view('auth.register');
    }
    
    public function register(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:moradors',
            'telefone' => 'nullable|string|max:20',
            'apartamento' => 'required|string|max:10',
            'bloco' => 'nullable|string|max:10',
            'cpf' => 'required|string|unique:moradors|max:14',
            'password' => 'required|string|min:6|confirmed'
        ]);
        
        $morador = \App\Models\Morador::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'apartamento' => $request->apartamento,
            'bloco' => $request->bloco,
            'cpf' => $request->cpf,
            'password' => password_hash($request->password, PASSWORD_DEFAULT),
            'ativo' => true
        ]);
        
        session(['morador_id' => $morador->id]);
        return redirect()->route('dashboard')->with('success', 'Conta criada com sucesso!');
    }
    
    public function logout()
    {
        session()->forget('morador_id');
        return redirect()->route('home');
    }
}
