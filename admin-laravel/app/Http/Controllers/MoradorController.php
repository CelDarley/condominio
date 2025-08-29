<?php

namespace App\Http\Controllers;

use App\Models\Morador;
use App\Models\Veiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MoradorController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $moradores = Morador::with('veiculos')->orderBy('nome')->get();
        return view('admin.moradores.index', compact('moradores'));
    }

    public function create()
    {
        return view('admin.moradores.create');
    }

    public function store(Request $request)
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
            'ativo' => true
        ]);

        return redirect()->route('admin.moradores.index')
            ->with('success', 'Morador cadastrado com sucesso!');
    }

    public function show(Morador $morador)
    {
        $morador->load('veiculos');
        return view('admin.moradores.show', compact('morador'));
    }

    public function edit(Morador $morador)
    {
        return view('admin.moradores.edit', compact('morador'));
    }

    public function update(Request $request, Morador $morador)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:moradores,email,' . $morador->id,
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'required|string|max:255',
            'apartamento' => 'required|string|max:10',
            'bloco' => 'nullable|string|max:10',
            'cpf' => 'required|string|unique:moradores,cpf,' . $morador->id . '|max:14'
        ]);

        $morador->update($request->only([
            'nome', 'email', 'telefone', 'endereco', 'apartamento', 'bloco', 'cpf'
        ]));

        return redirect()->route('admin.moradores.index')
            ->with('success', 'Morador atualizado com sucesso!');
    }

    public function destroy(Morador $morador)
    {
        $morador->delete();
        return redirect()->route('admin.moradores.index')
            ->with('success', 'Morador excluído com sucesso!');
    }

    public function toggleStatus(Morador $morador)
    {
        $morador->update(['ativo' => !$morador->ativo]);
        $status = $morador->ativo ? 'ativado' : 'desativado';
        
        return redirect()->route('admin.moradores.index')
            ->with('success', "Morador {$status} com sucesso!");
    }

    public function changePassword(Request $request, Morador $morador)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed'
        ]);

        $morador->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('admin.moradores.show', $morador)
            ->with('success', 'Senha alterada com sucesso!');
    }

    public function addVeiculo(Request $request, Morador $morador)
    {
        $request->validate([
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'placa' => 'required|string|unique:veiculos|max:10',
            'cor' => 'required|string|max:50'
        ]);

        $morador->veiculos()->create($request->only(['marca', 'modelo', 'placa', 'cor']));

        return redirect()->route('admin.moradores.show', $morador)
            ->with('success', 'Veículo adicionado com sucesso!');
    }

    public function removeVeiculo(Morador $morador, Veiculo $veiculo)
    {
        if ($veiculo->morador_id === $morador->id) {
            $veiculo->delete();
            return redirect()->route('admin.moradores.show', $morador)
                ->with('success', 'Veículo removido com sucesso!');
        }

        return redirect()->route('admin.moradores.show', $morador)
            ->with('error', 'Erro ao remover veículo!');
    }
}
