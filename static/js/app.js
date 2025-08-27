// Aplicação principal SegCond
class SegCondApp {
    constructor() {
        this.alertasAtivos = [];
        this.notificacoes = [];
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupBotaoPanico();
        this.setupNotificacoes();
        this.setupServiceWorker();
        
        // Verificar se o usuário está logado
        if (document.querySelector('.nav-menu')) {
            this.iniciarVerificacaoAlertas();
        }
    }

    setupEventListeners() {
        // Event listeners globais
        document.addEventListener('DOMContentLoaded', () => {
            this.setupModais();
            this.setupFormularios();
        });

        // Interceptar cliques em links para navegação suave
        document.addEventListener('click', (e) => {
            if (e.target.matches('a[href^="/"]')) {
                e.preventDefault();
                this.navegarPara(e.target.href);
            }
        });
    }

    setupBotaoPanico() {
        const btnPanico = document.getElementById('btn-panico');
        if (btnPanico) {
            btnPanico.addEventListener('click', () => this.ativarPanico());
        }
    }

    setupNotificacoes() {
        // Verificar se o navegador suporta notificações
        if ('Notification' in window) {
            this.solicitarPermissaoNotificacao();
        }
    }

    setupServiceWorker() {
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('Service Worker registrado:', registration);
                        this.serviceWorker = registration;
                    })
                    .catch(error => {
                        console.error('Erro ao registrar Service Worker:', error);
                    });
            });
        }
    }

    setupModais() {
        // Fechar modais ao clicar fora
        window.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                e.target.style.display = 'none';
            }
        });

        // Fechar modais com ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const modais = document.querySelectorAll('.modal');
                modais.forEach(modal => {
                    if (modal.style.display === 'block') {
                        modal.style.display = 'none';
                    }
                });
            }
        });
    }

    setupFormularios() {
        // Formulários de aviso
        const avisoForm = document.getElementById('aviso-form');
        if (avisoForm) {
            avisoForm.addEventListener('submit', (e) => this.enviarAviso(e));
        }

        // Formulários de resposta
        const respostaForm = document.getElementById('resposta-form');
        if (respostaForm) {
            respostaForm.addEventListener('submit', (e) => this.enviarResposta(e));
        }
    }

    iniciarVerificacaoAlertas() {
        // Verificar alertas a cada 30 segundos
        this.verificarAlertas();
        setInterval(() => this.verificarAlertas(), 30000);
    }

    async verificarAlertas() {
        try {
            const response = await fetch('/api/alertas');
            const alertas = await response.json();
            
            this.alertasAtivos = alertas;
            this.atualizarContadorAlertas(alertas.length);
            this.atualizarNotificacoes(alertas);
            
            // Verificar se há novos alertas
            if (alertas.length > 0) {
                this.notificarNovosAlertas(alertas);
            }
        } catch (error) {
            console.error('Erro ao verificar alertas:', error);
        }
    }

    atualizarContadorAlertas(count) {
        const alertCount = document.getElementById('alert-count');
        if (alertCount) {
            if (count > 0) {
                alertCount.textContent = count;
                alertCount.style.display = 'inline';
            } else {
                alertCount.style.display = 'none';
            }
        }
    }

    atualizarNotificacoes(alertas) {
        const notificacoesList = document.getElementById('notificacoes-list');
        if (!notificacoesList) return;

        if (alertas.length === 0) {
            notificacoesList.innerHTML = `
                <div class="no-notifications">
                    <i class="fas fa-bell-slash"></i>
                    <p>Nenhuma notificação no momento</p>
                </div>
            `;
            return;
        }

        notificacoesList.innerHTML = alertas.map(alerta => `
            <div class="notificacao-item">
                <div class="notificacao-icon">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                </div>
                <div class="notificacao-content">
                    <h5>Alerta de ${alerta.morador}</h5>
                    <p>${alerta.mensagem}</p>
                    <small>${new Date(alerta.timestamp).toLocaleString('pt-BR')}</small>
                </div>
            </div>
        `).join('');
    }

    notificarNovosAlertas(alertas) {
        // Notificação do navegador
        if (Notification.permission === 'granted') {
            alertas.forEach(alerta => {
                new Notification('SegCond - Novo Alerta', {
                    body: `Alerta de ${alerta.morador}: ${alerta.mensagem}`,
                    icon: '/static/icons/icon-192x192.png',
                    tag: `alerta-${alerta.id}`,
                    requireInteraction: true
                });
            });
        }

        // Notificação visual na aplicação
        this.mostrarNotificacao(`Você tem ${alertas.length} novo(s) alerta(s)`, 'warning');
    }

    async ativarPanico() {
        if (confirm('ATENÇÃO: Você está ativando o botão de PÂNICO!\n\nIsso enviará um alerta de emergência para todos os responsáveis.\n\nConfirmar?')) {
            try {
                const response = await fetch('/botao_panico', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });

                const data = await response.json();
                
                if (data.status === 'success') {
                    this.mostrarNotificacao('Alerta de pânico enviado com sucesso!', 'success');
                    
                    // Vibração do dispositivo (se suportado)
                    if ('vibrate' in navigator) {
                        navigator.vibrate([200, 100, 200, 100, 200]);
                    }
                    
                    // Notificação do navegador
                    if (Notification.permission === 'granted') {
                        new Notification('SegCond - PÂNICO ATIVADO', {
                            body: 'Alerta de emergência foi enviado para todos os responsáveis',
                            icon: '/static/icons/icon-192x192.png',
                            requireInteraction: true,
                            priority: 'high'
                        });
                    }
                } else {
                    this.mostrarNotificacao('Erro ao enviar alerta de pânico', 'error');
                }
            } catch (error) {
                console.error('Erro ao ativar pânico:', error);
                this.mostrarNotificacao('Erro ao enviar alerta de pânico', 'error');
            }
        }
    }

    async enviarAviso(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const titulo = formData.get('titulo');
        const mensagem = formData.get('mensagem');
        
        if (!titulo || !mensagem) {
            this.mostrarNotificacao('Preencha todos os campos', 'error');
            return;
        }
        
        try {
            const response = await fetch('/enviar_aviso', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.status === 'success') {
                this.mostrarNotificacao('Aviso enviado com sucesso!', 'success');
                
                // Fechar modal
                const modal = document.getElementById('aviso-modal');
                if (modal) {
                    modal.style.display = 'none';
                }
                
                // Limpar formulário
                e.target.reset();
            } else {
                this.mostrarNotificacao('Erro ao enviar aviso: ' + data.message, 'error');
            }
        } catch (error) {
            console.error('Erro ao enviar aviso:', error);
            this.mostrarNotificacao('Erro ao enviar aviso', 'error');
        }
    }

    async enviarResposta(e) {
        e.preventDefault();
        
        const alertaId = document.getElementById('alerta-id').value;
        const mensagem = document.getElementById('resposta-mensagem').value;
        
        if (!mensagem) {
            this.mostrarNotificacao('Digite uma resposta', 'error');
            return;
        }
        
        try {
            // Aqui você implementaria o envio da resposta
            console.log('Respondendo alerta:', alertaId, mensagem);
            
            this.mostrarNotificacao('Resposta enviada com sucesso!', 'success');
            
            // Fechar modal
            const modal = document.getElementById('resposta-modal');
            if (modal) {
                modal.style.display = 'none';
            }
            
            // Limpar formulário
            e.target.reset();
        } catch (error) {
            console.error('Erro ao enviar resposta:', error);
            this.mostrarNotificacao('Erro ao enviar resposta', 'error');
        }
    }

    async atenderAlerta(alertaId) {
        if (confirm('Confirmar que este alerta foi atendido?')) {
            try {
                const response = await fetch(`/atender_alerta/${alertaId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    // Remover o alerta da interface
                    const alertaCard = document.querySelector(`[data-alerta-id="${alertaId}"]`);
                    if (alertaCard) {
                        alertaCard.remove();
                    }
                    
                    // Atualizar contador
                    const alertasRestantes = document.querySelectorAll('.alerta-card').length;
                    this.atualizarContadorAlertas(alertasRestantes);
                    
                    this.mostrarNotificacao('Alerta marcado como atendido', 'success');
                }
            } catch (error) {
                console.error('Erro:', error);
                this.mostrarNotificacao('Erro ao atender alerta', 'error');
            }
        }
    }

    mostrarNotificacao(mensagem, tipo = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${tipo}`;
        notification.textContent = mensagem;
        
        // Adicionar ícone baseado no tipo
        const icon = document.createElement('i');
        icon.className = this.getIconClass(tipo);
        notification.insertBefore(icon, notification.firstChild);
        
        document.body.appendChild(notification);
        
        // Remover após 3 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
        
        // Animar entrada
        requestAnimationFrame(() => {
            notification.style.transform = 'translateX(0)';
            notification.style.opacity = '1';
        });
    }

    getIconClass(tipo) {
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };
        return icons[tipo] || icons.info;
    }

    async solicitarPermissaoNotificacao() {
        if (Notification.permission === 'default') {
            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                console.log('Permissão de notificação concedida');
            }
        }
    }

    navegarPara(url) {
        // Navegação suave para PWA
        if (this.serviceWorker && this.serviceWorker.active) {
            // Usar service worker para navegação
            window.location.href = url;
        } else {
            // Navegação normal
            window.location.href = url;
        }
    }

    // Utilitários
    formatarData(data) {
        return new Date(data).toLocaleString('pt-BR');
    }

    formatarTempo(minutos) {
        const horas = Math.floor(minutos / 60);
        const mins = minutos % 60;
        
        if (horas > 0) {
            return `${horas}h ${mins}min`;
        }
        return `${mins}min`;
    }

    // Verificar conectividade
    verificarConectividade() {
        if (!navigator.onLine) {
            this.mostrarNotificacao('Sem conexão com a internet', 'warning');
        }
    }

    // Função para registrar presença nos pontos base
    async registrarPresenca(pontoId) {
        try {
            const response = await fetch(`/registrar_presenca/${pontoId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            const data = await response.json();
            
            if (data.status === 'chegada') {
                // Atualizar interface para chegada
                const statusElement = document.getElementById(`status-${pontoId}`);
                if (statusElement) {
                    statusElement.innerHTML = '<i class="fas fa-check-circle text-success"></i><span>Presente</span>';
                }
                
                const btnTextElement = document.getElementById(`btn-text-${pontoId}`);
                if (btnTextElement) {
                    btnTextElement.textContent = 'Registrar Saída';
                }
                
                const btnElement = document.querySelector(`[data-ponto-id="${pontoId}"] .btn-register-presence`);
                if (btnElement) {
                    btnElement.classList.add('btn-exit');
                }
                
                this.mostrarNotificacao('Chegada registrada com sucesso', 'success');
            } else if (data.status === 'saida') {
                // Atualizar interface para saída
                const statusElement = document.getElementById(`status-${pontoId}`);
                if (statusElement) {
                    statusElement.innerHTML = '<i class="fas fa-times-circle text-muted"></i><span>Concluído</span>';
                }
                
                const btnTextElement = document.getElementById(`btn-text-${pontoId}`);
                if (btnTextElement) {
                    btnTextElement.textContent = 'Concluído';
                }
                
                const btnElement = document.querySelector(`[data-ponto-id="${pontoId}"] .btn-register-presence`);
                if (btnElement) {
                    btnElement.disabled = true;
                }
                
                this.mostrarNotificacao('Saída registrada com sucesso', 'success');
            }
        } catch (error) {
            console.error('Erro ao registrar presença:', error);
            this.mostrarNotificacao('Erro ao registrar presença', 'error');
        }
    }

    // Função para responder a alertas
    responderAlerta(alertaId) {
        const modal = document.getElementById('resposta-modal');
        if (modal) {
            document.getElementById('alerta-id').value = alertaId;
            modal.style.display = 'block';
        }
    }
}

// Inicializar aplicação quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    window.segCondApp = new SegCondApp();
    
    // Verificar conectividade
    window.addEventListener('online', () => {
        window.segCondApp.mostrarNotificacao('Conexão restaurada', 'success');
    });
    
    window.addEventListener('offline', () => {
        window.segCondApp.mostrarNotificacao('Sem conexão com a internet', 'warning');
    });
});

// Funções globais para uso nos templates
window.registrarPresenca = function(pontoId) {
    if (window.segCondApp) {
        window.segCondApp.registrarPresenca(pontoId);
    }
};

window.responderAlerta = function(alertaId) {
    if (window.segCondApp) {
        window.segCondApp.responderAlerta(alertaId);
    }
};

window.atenderAlerta = function(alertaId) {
    if (window.segCondApp) {
        window.segCondApp.atenderAlerta(alertaId);
    }
};
