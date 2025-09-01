@extends("layouts.app")

@section("title", "Escala Diária")
@section("page-title", "Escala Diária")

@section("content")
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-day me-2"></i>Gerenciamento de Escala Diária
                    </h6>
                    <div class="d-flex align-items-center">
                        <!-- Filtro por Vigilante -->
                        <div class="me-3">
                            <label for="filtro-vigilante" class="form-label mb-1 small">Filtrar por Vigilante:</label>
                            <select class="form-select form-select-sm" id="filtro-vigilante" onchange="filtrarPorVigilante()">
                                <option value="">Todos os vigilantes</option>
                                @foreach($vigilantes as $vigilante)
                                    <option value="{{ $vigilante->id }}">{{ $vigilante->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Navegação de mês -->
                        <button class="btn btn-outline-primary btn-sm me-2" onclick="navegarMes(-1)">
                            <i class="fas fa-chevron-left"></i> Anterior
                        </button>
                        <span class="h6 mb-0 me-2" id="mes-ano-display">
                            {{ \Carbon\Carbon::create($ano, $mes)->locale('pt_BR')->format('F Y') }}
                        </span>
                        <button class="btn btn-outline-primary btn-sm me-3" onclick="navegarMes(1)">
                            Próximo <i class="fas fa-chevron-right"></i>
                        </button>
                        <button class="btn btn-info btn-sm" onclick="voltarHoje()">
                            <i class="fas fa-home"></i> Hoje
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Informações sobre uso -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Como usar a Escala Diária:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Clique em um dia do calendário para ver/gerenciar as escalas</li>
                                <li>Substitua vigilantes quando necessário (licença, falta, etc.)</li>
                                <li>Altere cartões programa específicos para o dia</li>
                                <li>Dias com ajustes aparecem destacados em azul</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Calendário -->
                <div class="table-responsive">
                    <table class="table table-bordered calendar-table">
                        <thead class="table-primary">
                            <tr>
                                <th class="text-center">Domingo</th>
                                <th class="text-center">Segunda</th>
                                <th class="text-center">Terça</th>
                                <th class="text-center">Quarta</th>
                                <th class="text-center">Quinta</th>
                                <th class="text-center">Sexta</th>
                                <th class="text-center">Sábado</th>
                            </tr>
                        </thead>
                        <tbody id="calendario-body">
                            <!-- Preenchido via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Legenda -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex flex-wrap align-items-center">
                            <small class="text-muted me-4">
                                <span class="badge bg-secondary me-1">&nbsp;</span> Dia normal
                            </small>
                            <small class="text-muted me-4">
                                <span class="badge bg-primary me-1">&nbsp;</span> Com ajustes
                            </small>
                            <small class="text-muted me-4">
                                <span class="badge bg-success me-1">&nbsp;</span> Hoje
                            </small>
                            <small class="text-muted me-4" id="legenda-vigilante" style="display: none;">
                                <span class="badge bg-info me-1">&nbsp;</span> Escala semanal
                                <span class="badge bg-warning me-1">&nbsp;</span> Substituição
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para gerenciar escalas do dia -->
<div class="modal fade" id="modalEscalaDia" tabindex="-1" aria-labelledby="modalEscalaDiaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEscalaDiaLabel">
                    <i class="fas fa-calendar-day me-2"></i>Escalas do Dia
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-escala-body">
                <!-- Conteúdo carregado via AJAX -->
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2">Carregando escalas...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para criar/editar ajuste -->
<div class="modal fade" id="modalAjuste" tabindex="-1" aria-labelledby="modalAjusteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAjusteLabel">
                    <i class="fas fa-user-edit me-2"></i>Ajuste de Escala
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-ajuste">
                <div class="modal-body">
                    <input type="hidden" id="ajuste-data" name="data">
                    <input type="hidden" id="ajuste-escala-id" name="escala_original_id">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Data:</label>
                            <p class="form-control-plaintext" id="ajuste-data-display"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Posto de Trabalho:</label>
                            <p class="form-control-plaintext" id="ajuste-posto-display"></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Usuário Original:</label>
                            <p class="form-control-plaintext" id="ajuste-usuario-original-display"></p>
                        </div>
                        <div class="col-md-6">
                            <label for="ajuste-usuario-substituto" class="form-label">Novo Usuário: <span class="text-danger">*</span></label>
                            <select class="form-control" id="ajuste-usuario-substituto" name="usuario_substituto_id" required>
                                <option value="">Selecione um substituto</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="ajuste-cartao-programa" class="form-label">Cartão Programa:</label>
                            <select class="form-control" id="ajuste-cartao-programa" name="cartao_programa_id">
                                <option value="">Manter cartão original</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="ajuste-motivo" class="form-label">Motivo:</label>
                            <textarea class="form-control" id="ajuste-motivo" name="motivo" rows="3" placeholder="Ex: Licença médica, falta justificada..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Salvar Ajuste
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.calendar-table {
    font-size: 0.875rem;
}

.calendar-table td {
    height: 120px;
    vertical-align: top;
    position: relative;
    cursor: pointer;
    transition: background-color 0.2s;
}

.calendar-table td:hover {
    background-color: #f8f9fc;
}

.day-number {
    font-weight: bold;
    margin-bottom: 5px;
}

.day-content {
    font-size: 0.75rem;
}

.day-with-adjustments {
    background-color: #e3f2fd !important;
    border-left: 4px solid #2196f3;
}

.day-today {
    background-color: #e8f5e8 !important;
    border-left: 4px solid #4caf50;
}

.day-other-month {
    color: #ccc;
    background-color: #f8f9fa;
}

.adjustment-badge {
    position: absolute;
    top: 2px;
    right: 2px;
    font-size: 0.6rem;
}

.escala-ajustada-indicator .badge {
    background-color: #fd7e14 !important;
}

.day-with-vigilante-escala {
    background-color: #d1ecf1 !important;
    border-left: 4px solid #17a2b8;
}

.day-with-vigilante-escala-and-adjustment {
    background: linear-gradient(135deg, #d1ecf1 0%, #fff3cd 100%) !important;
    border-left: 4px solid #fd7e14;
    border-right: 4px solid #17a2b8;
}

.vigilante-badge {
    position: absolute;
    bottom: 2px;
    right: 2px;
    background-color: #17a2b8;
    color: white;
    font-size: 0.6rem;
    padding: 1px 4px;
    border-radius: 6px;
    line-height: 1;
}
</style>

<script>
let mesAtual = {{ $mes }};
let anoAtual = {{ $ano }};
let ajustesData = @json($ajustes);
let vigilanteSelecionado = @json($vigilanteSelecionado);
let vigilantesEscalas = {};

document.addEventListener('DOMContentLoaded', function() {
    // Restaurar vigilante selecionado se houver
    if (vigilanteSelecionado) {
        document.getElementById('filtro-vigilante').value = vigilanteSelecionado;
        document.getElementById('legenda-vigilante').style.display = 'inline-block';
        carregarEscalasVigilante(vigilanteSelecionado);
    } else {
        gerarCalendario();
    }
});

function navegarMes(direcao) {
    mesAtual += direcao;
    if (mesAtual > 12) {
        mesAtual = 1;
        anoAtual++;
    } else if (mesAtual < 1) {
        mesAtual = 12;
        anoAtual--;
    }

    // Construir URL com filtro de vigilante se houver
    let url = `?mes=${mesAtual}&ano=${anoAtual}`;
    if (vigilanteSelecionado) {
        url += `&vigilante=${vigilanteSelecionado}`;
    }

    window.location.href = url;
}

function voltarHoje() {
    const hoje = new Date();
    window.location.href = `?mes=${hoje.getMonth() + 1}&ano=${hoje.getFullYear()}`;
}

function gerarCalendario() {
    const primeiroDia = new Date(anoAtual, mesAtual - 1, 1);
    const ultimoDia = new Date(anoAtual, mesAtual, 0);
    const hoje = new Date();

    const inicioCalendario = new Date(primeiroDia);
    inicioCalendario.setDate(inicioCalendario.getDate() - primeiroDia.getDay());

    const tbody = document.getElementById('calendario-body');
    tbody.innerHTML = '';

    let dataAtual = new Date(inicioCalendario);

    for (let semana = 0; semana < 6; semana++) {
        const linha = document.createElement('tr');

        for (let dia = 0; dia < 7; dia++) {
            const celula = document.createElement('td');
            const dataStr = dataAtual.toISOString().split('T')[0];

            celula.onclick = () => abrirEscalasDia(dataStr);

            const isOutroMes = dataAtual.getMonth() !== mesAtual - 1;
            const isHoje = dataAtual.toDateString() === hoje.toDateString();
            const temAjustes = ajustesData[dataStr] && ajustesData[dataStr].length > 0;
            const temEscalaVigilante = vigilantesEscalas[dataStr] ? true : false;

            // Aplicar classes baseadas no estado
            let classes = ['day-cell'];
            if (isOutroMes) {
                classes.push('day-other-month');
            } else if (isHoje) {
                classes.push('day-today');
            }

            if (temAjustes && temEscalaVigilante) {
                classes.push('day-with-vigilante-escala-and-adjustment');
            } else if (temEscalaVigilante) {
                classes.push('day-with-vigilante-escala');
            } else if (temAjustes) {
                classes.push('day-with-adjustments');
            }

            celula.className = classes.join(' ');

            let badges = '';
            if (temAjustes) {
                badges += `<span class="badge bg-primary adjustment-badge">${ajustesData[dataStr].length}</span>`;
            }
            if (temEscalaVigilante) {
                const escalaInfo = vigilantesEscalas[dataStr];
                if (escalaInfo.tipo === 'substituicao') {
                    badges += `<span class="badge bg-warning vigilante-badge" title="Substituindo: ${escalaInfo.substituindo}">S</span>`;
                } else {
                    badges += `<span class="badge bg-info vigilante-badge" title="Escala semanal">V</span>`;
                }
            }

            celula.innerHTML = `
                <div class="day-number">${dataAtual.getDate()}</div>
                <div class="day-content">
                    ${badges}
                </div>
            `;

            linha.appendChild(celula);
            dataAtual.setDate(dataAtual.getDate() + 1);
        }

        tbody.appendChild(linha);

        // Parar se já passou do mês atual
        if (dataAtual.getMonth() !== mesAtual - 1 && semana >= 4) {
            break;
        }
    }
}

function abrirEscalasDia(data) {
    const modal = new bootstrap.Modal(document.getElementById('modalEscalaDia'));
    const modalBody = document.getElementById('modal-escala-body');

    modalBody.innerHTML = `
        <div class="text-center">
            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
            <p class="mt-2">Carregando escalas...</p>
        </div>
    `;

    modal.show();

    fetch(`/admin/escala-diaria/calendario?data=${data}`)
        .then(response => response.json())
        .then(data => {
            modalBody.innerHTML = montarConteudoModal(data);
        })
        .catch(error => {
            console.error('Erro:', error);
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Erro ao carregar escalas. Tente novamente.
                </div>
            `;
        });
}

function montarConteudoModal(data) {
    let html = `
        <div class="mb-3">
            <h6><i class="fas fa-calendar-day me-2"></i>${data.data_formatada} - ${data.dia_semana}</h6>
        </div>
    `;

    if (data.escalas.length === 0) {
        html += `
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-2"></i>
                <p class="mb-0">Nenhuma escala programada para este dia.</p>
            </div>
        `;
    } else {
        html += `<div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Posto</th>
                        <th>Usuário</th>
                        <th>Cartão Programa</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>`;

        data.escalas.forEach(escala => {
            const isAjustado = escala.tem_ajuste;
            const badgeClass = isAjustado ? 'bg-primary' : 'bg-success';
            const statusText = isAjustado ? 'Ajustado' : 'Normal';

            html += `
                <tr class="${isAjustado ? 'table-primary' : ''}">
                    <td>${escala.posto.nome}</td>
                    <td>
                        ${escala.usuario.nome}
                        ${isAjustado ? `<br><small class="text-muted">Original: ${escala.ajuste_diario.usuario_original}</small>` : ''}
                    </td>
                    <td>
                        ${escala.cartao_programa ? escala.cartao_programa.nome + '<br><small>' + escala.cartao_programa.horario + '</small>' : 'Não definido'}
                    </td>
                    <td><span class="badge ${badgeClass}">${statusText}</span></td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editarAjuste('${data.data}', ${escala.id}, ${escala.posto.id}, '${escala.posto.nome}', '${escala.usuario.nome}', ${isAjustado ? escala.ajuste_diario.id : 'null'})">
                            <i class="fas fa-edit"></i> ${isAjustado ? 'Editar' : 'Ajustar'}
                        </button>
                        ${isAjustado ? `
                            <button class="btn btn-danger btn-sm ms-1" onclick="cancelarAjuste(${escala.ajuste_diario.id})">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        ` : ''}
                    </td>
                </tr>
            `;
        });

        html += `</tbody></table></div>`;
    }

    return html;
}

// Resto das funções JavaScript será adicionado na próxima parte...

let usuariosDisponiveis = [];
let cartoesPrograma = [];

function editarAjuste(data, escalaId, postoId, postoNome, usuarioOriginal, ajusteId = null) {
    // Carregar usuários disponíveis
    fetch('/admin/escala-diaria/calendario?data=' + data)
        .then(response => response.json())
        .then(responseData => {
            usuariosDisponiveis = responseData.usuarios_disponiveis;

            // Preencher modal
            document.getElementById('ajuste-data').value = data;
            document.getElementById('ajuste-escala-id').value = escalaId;
            document.getElementById('ajuste-data-display').textContent = responseData.data_formatada;
            document.getElementById('ajuste-posto-display').textContent = postoNome;
            document.getElementById('ajuste-usuario-original-display').textContent = usuarioOriginal;

            // Preencher select de usuários
            const selectUsuario = document.getElementById('ajuste-usuario-substituto');
            selectUsuario.innerHTML = '<option value="">Selecione um substituto</option>';
            usuariosDisponiveis.forEach(user => {
                selectUsuario.innerHTML += `<option value="${user.id}">${user.nome}</option>`;
            });

            // Carregar cartões programa do posto
            carregarCartoesPrograma(postoId);

            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('modalAjuste'));
            modal.show();
        })
        .catch(error => {
            console.error('Erro ao carregar dados:', error);
            alert('Erro ao carregar dados. Tente novamente.');
        });
}

function carregarCartoesPrograma(postoId) {
    fetch(`/admin/escala-diaria/cartoes-programa?posto_id=${postoId}`)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('ajuste-cartao-programa');
            select.innerHTML = '<option value="">Manter cartão original</option>';

            data.forEach(cartao => {
                select.innerHTML += `<option value="${cartao.id}">${cartao.nome} (${cartao.horario_inicio} - ${cartao.horario_fim})</option>`;
            });
        })
        .catch(error => {
            console.error('Erro ao carregar cartões:', error);
        });
}

function cancelarAjuste(ajusteId) {
    if (!confirm('Tem certeza que deseja cancelar este ajuste?')) {
        return;
    }

    fetch(`/admin/escala-diaria/${ajusteId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload(); // Recarregar página para atualizar calendário
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao cancelar ajuste.');
    });
}

// Manipular formulário de ajuste
document.getElementById('form-ajuste').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData);

    fetch('/admin/escala-diaria', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            // Fechar modal
            bootstrap.Modal.getInstance(document.getElementById('modalAjuste')).hide();
            // Recarregar página para atualizar calendário
            location.reload();
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao salvar ajuste.');
    });
});

function filtrarPorVigilante() {
    vigilanteSelecionado = document.getElementById('filtro-vigilante').value;
    const legendaVigilante = document.getElementById('legenda-vigilante');

    if (vigilanteSelecionado) {
        // Mostrar legenda do vigilante
        legendaVigilante.style.display = 'inline-block';

        // Carregar escalas do vigilante para o mês
        carregarEscalasVigilante(vigilanteSelecionado);
    } else {
        // Ocultar legenda e remover destaque
        legendaVigilante.style.display = 'none';
        vigilantesEscalas = {};
        gerarCalendario();
    }
}

function carregarEscalasVigilante(vigilanteId) {
    fetch(`/admin/api/escalas-vigilante/${vigilanteId}/${anoAtual}/${mesAtual}`)
        .then(response => response.json())
        .then(data => {
            vigilantesEscalas = data.escalas || {};
            gerarCalendario();
        })
        .catch(error => {
            console.error('Erro ao carregar escalas do vigilante:', error);
        });
}
</script>

@endsection
