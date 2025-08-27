<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\PostoTrabalho;
use App\Models\Escala;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['dashboard', 'logout']);
    }

    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            if ($user->tipo === 'admin') {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            } else {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Acesso restrito a administradores.',
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ]);
    }

    public function dashboard()
    {
        $data = [
            'totalUsuarios' => Usuario::count(),
            'vigilantesAtivos' => Usuario::where('tipo', 'vigilante')->where('ativo', true)->count(),
            'totalPostos' => PostoTrabalho::count(),
            'escalasAtivas' => Escala::where('ativo', true)->count(),
            'usuariosRecentes' => Usuario::latest()->take(5)->get()
        ];

        return view('admin.dashboard', $data);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }
}
