@extends('layouts.app')

@section('title', 'Moradores')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-users me-2"></i>Gerenciar Moradores</h2>
    <a href="{{ route('admin.moradores.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Novo Morador
    </a>
</div>

@if($moradores->count() > 0)
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Apartamento</th>
                            <th>Veículos</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($moradores as $morador)
                            <tr>
                                <td>{{ $morador->nome }}</td>
                                <td>{{ $morador->email }}</td>
                                <td>{{ $morador->telefone ?? '-' }}</td>
                                <td>
                                    {{ $morador->apartamento }}
                                    @if($morador->bloco)
                                        - Bloco {{ $morador->bloco }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $morador->veiculos->count() }} veículo(s)</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $morador->ativo ? 'success' : 'danger' }}">
                                        {{ $morador->ativo ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.moradores.show', $morador) }}" class="btn btn-info btn-sm" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.moradores.edit', $morador) }}" class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <form method="POST" action="{{ route('admin.moradores.toggle-status', $morador) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-{{ $morador->ativo ? 'secondary' : 'success' }} btn-sm" 
                                                    title="{{ $morador->ativo ? 'Desativar' : 'Ativar' }}">
                                                <i class="fas fa-{{ $morador->ativo ? 'ban' : 'check' }}"></i>
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.moradores.destroy', $morador) }}" 
                                              style="display: inline;" 
                                              onsubmit="return confirm('Tem certeza que deseja excluir este morador?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info text-center">
        <i class="fas fa-info-circle me-2"></i>
        Nenhum morador cadastrado ainda.
        <br><br>
        <a href="{{ route('admin.moradores.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Cadastrar Primeiro Morador
        </a>
    </div>
@endif
@endsection 