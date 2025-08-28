<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\PostoTrabalho;
use App\Models\Escala;
use App\Models\PontoBase;

class AdminController extends Controller
{
    public function __construct()
    {
        // Configurar o guard de autenticação para usar o modelo Usuario
        Auth::setDefaultDriver('web');
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

        $email = $request->input('email');
        $password = $request->input('password');

        // Buscar usuário por email
        $usuario = Usuario::where('email', $email)->first();

        if ($usuario && Hash::check($password, $usuario->senha_hash)) {
            // Verificar se é admin
            if ($usuario->tipo === 'admin' && $usuario->ativo) {
                Auth::login($usuario);
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            } else {
                return back()->withErrors([
                    'email' => 'Acesso restrito a administradores ativos.',
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ]);
    }

    public function dashboard()
    {
        // Verificar se o usuário está autenticado e é admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect()->route('admin.login')->with('error', 'Acesso negado.');
        }

        // Dados do dashboard com informações reais
        $data = [
            'totalUsuarios' => Usuario::count(),
            'adminCount' => Usuario::where('tipo', 'admin')->count(),
            'vigilantesAtivos' => Usuario::where('tipo', 'vigilante')->where('ativo', true)->count(),
            'totalPostos' => PostoTrabalho::ativos()->count(),
            'totalPontosBases' => PontoBase::ativos()->count(),
            'escalasAtivas' => Escala::ativos()->count(),
            'usuariosRecentes' => Usuario::latest('data_criacao')->take(5)->get(),
            'usuariosAdmin' => Usuario::where('tipo', 'admin')->get(),
            
            // Estatísticas por tipo
            'estatisticasTipos' => [
                'admin' => Usuario::where('tipo', 'admin')->count(),
                'vigilante' => Usuario::where('tipo', 'vigilante')->count(),
                'morador' => Usuario::where('tipo', 'morador')->count(),
            ],
            
            // Escalas por dia da semana
            'escalasPorDia' => Escala::selectRaw('dia_semana, COUNT(*) as total')
                ->where('ativo', true)
                ->groupBy('dia_semana')
                ->pluck('total', 'dia_semana')
                ->toArray(),
                
            // Postos com mais pontos base
            'postosComPontos' => PostoTrabalho::withCount(['pontosBase' => function($query) {
                $query->where('ativo', true);
            }])
            ->ativos()
            ->orderByDesc('pontos_base_count')
            ->take(5)
            ->get()
        ];

        return view('admin.dashboard', $data);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')->with('success', 'Logout realizado com sucesso.');
    }
}
