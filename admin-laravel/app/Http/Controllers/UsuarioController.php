<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $usuarios = Usuario::where('tipo', '!=', 'admin')->get();
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'email' => 'required|email|max:120',
            'telefone' => 'nullable|string|max:20',
            'senha' => 'required|string|min:6',
            'tipo' => 'required|in:vigilante,morador'
        ]);

        // Verificar se o email já existe em usuários ativos
        $existingUser = Usuario::where('email', $request->email)->where('ativo', true)->first();
        if ($existingUser) {
            return back()->withErrors(['email' => 'Este email já está em uso por um usuário ativo.'])->withInput();
        }

        // Se existir usuário inativo com este email, reativar e atualizar
        $inactiveUser = Usuario::where('email', $request->email)->where('ativo', false)->first();
        if ($inactiveUser) {
            $inactiveUser->update([
                'nome' => $request->nome,
                'telefone' => $request->telefone,
                'senha_hash' => Hash::make($request->senha),
                'tipo' => $request->tipo,
                'ativo' => true,
                'data_atualizacao' => now()
            ]);

            return redirect()->route('admin.usuarios.index')
                ->with('success', 'Usuário reativado e atualizado com sucesso!');
        }

        // Criar novo usuário
        Usuario::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'senha_hash' => Hash::make($request->senha),
            'tipo' => $request->tipo,
            'ativo' => true,
            'data_criacao' => now()
        ]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    public function show(Usuario $usuario)
    {
        return view('admin.usuarios.show', compact('usuario'));
    }

    public function edit(Usuario $usuario)
    {
        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'email' => 'required|email|max:120|unique:usuario,email,' . $usuario->id,
            'telefone' => 'nullable|string|max:20',
            'tipo' => 'required|in:vigilante,morador,admin',
            'ativo' => 'boolean'
        ]);

        $data = [
            'nome' => $request->nome,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'tipo' => $request->tipo,
            'ativo' => $request->has('ativo')
        ];

        if ($request->filled('nova_senha')) {
            $data['senha_hash'] = Hash::make($request->nova_senha);
        }

        $usuario->update($data);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(Usuario $usuario)
    {
        // Soft delete - apenas desativa o usuário
        $usuario->update(['ativo' => false]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuário desativado com sucesso!');
    }

    public function forceDelete(Usuario $usuario)
    {
        // Verificar se o usuário tem relacionamentos
        if ($usuario->escalas()->count() > 0) {
            return redirect()->route('admin.usuarios.index')
                ->with('error', 'Não é possível excluir usuário que possui escalas associadas. Desative-o primeiro.');
        }

        // Exclusão permanente
        $usuario->delete();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuário excluído permanentemente com sucesso!');
    }

    public function deactivate(Usuario $usuario)
    {
        // Soft delete - apenas desativa o usuário
        $usuario->update(['ativo' => false]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuário desativado com sucesso!');
    }

    public function toggleStatus(Usuario $usuario)
    {
        $usuario->update(['ativo' => !$usuario->ativo]);

        $status = $usuario->ativo ? 'ativado' : 'desativado';
        return redirect()->route('admin.usuarios.index')
            ->with('success', "Usuário {$status} com sucesso!");
    }
}
