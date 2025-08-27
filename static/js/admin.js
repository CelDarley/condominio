// SegCond Admin - JavaScript Functions

class SegCondAdmin {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeTooltips();
        this.initializeDataTables();
    }

    setupEventListeners() {
        // Confirmar exclusões
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-delete')) {
                e.preventDefault();
                this.confirmDelete(e.target);
            }
        });

        // Toggle de status
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('status-toggle')) {
                this.toggleStatus(e.target);
            }
        });

        // Filtros de tabela
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('table-filter')) {
                this.filterTable(e.target);
            }
        });

        // Validação de formulários
        document.addEventListener('submit', (e) => {
            if (e.target.classList.contains('needs-validation')) {
                if (!this.validateForm(e.target)) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                e.target.classList.add('was-validated');
            }
        });
    }

    initializeTooltips() {
        // Inicializar tooltips do Bootstrap
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    initializeDataTables() {
        // Inicializar DataTables se disponível
        if (typeof $.fn.DataTable !== 'undefined') {
            $('.datatable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
                },
                responsive: true,
                pageLength: 25,
                order: [[0, 'asc']]
            });
        }
    }

    confirmDelete(button) {
        const message = button.dataset.message || 'Tem certeza que deseja excluir este item?';
        const title = button.dataset.title || 'Confirmar Exclusão';
        
        if (confirm(message)) {
            const form = button.closest('form');
            if (form) {
                form.submit();
            } else {
                window.location.href = button.href;
            }
        }
    }

    toggleStatus(toggle) {
        const itemId = toggle.dataset.id;
        const itemType = toggle.dataset.type;
        const newStatus = toggle.checked;
        
        // Mostrar loading
        toggle.disabled = true;
        
        // Fazer requisição AJAX para atualizar status
        fetch(`/admin/api/${itemType}/${itemId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('Status atualizado com sucesso!', 'success');
            } else {
                this.showNotification('Erro ao atualizar status', 'error');
                toggle.checked = !newStatus; // Reverter mudança
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            this.showNotification('Erro ao atualizar status', 'error');
            toggle.checked = !newStatus; // Reverter mudança
        })
        .finally(() => {
            toggle.disabled = false;
        });
    }

    filterTable(input) {
        const filterValue = input.value.toLowerCase();
        const table = input.closest('.table-responsive').querySelector('table');
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(filterValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    validateForm(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                this.showFieldError(field, 'Este campo é obrigatório');
                isValid = false;
            } else {
                this.clearFieldError(field);
            }
        });

        // Validação de email
        const emailFields = form.querySelectorAll('input[type="email"]');
        emailFields.forEach(field => {
            if (field.value && !this.isValidEmail(field.value)) {
                this.showFieldError(field, 'Email inválido');
                isValid = false;
            }
        });

        // Validação de telefone
        const phoneFields = form.querySelectorAll('input[data-type="phone"]');
        phoneFields.forEach(field => {
            if (field.value && !this.isValidPhone(field.value)) {
                this.showFieldError(field, 'Telefone inválido');
                isValid = false;
            }
        });

        return isValid;
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    isValidPhone(phone) {
        const phoneRegex = /^[\d\s\-\+\(\)]+$/;
        return phoneRegex.test(phone) && phone.replace(/\D/g, '').length >= 10;
    }

    showFieldError(field, message) {
        this.clearFieldError(field);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        
        field.classList.add('is-invalid');
        field.parentNode.appendChild(errorDiv);
    }

    clearFieldError(field) {
        field.classList.remove('is-invalid');
        const errorDiv = field.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    showNotification(message, type = 'info') {
        // Criar notificação toast
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        // Adicionar ao container de toasts
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        
        toastContainer.appendChild(toast);
        
        // Mostrar toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Remover após ser escondido
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    // Função para carregar dados via AJAX
    async loadData(url, targetElement) {
        try {
            targetElement.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
            
            const response = await fetch(url);
            const data = await response.text();
            
            targetElement.innerHTML = data;
        } catch (error) {
            console.error('Erro ao carregar dados:', error);
            targetElement.innerHTML = '<div class="alert alert-danger">Erro ao carregar dados</div>';
        }
    }

    // Função para enviar formulário via AJAX
    async submitForm(form, successCallback = null) {
        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: form.method,
                body: formData
            });
            
            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    this.showNotification(result.message || 'Operação realizada com sucesso!', 'success');
                    if (successCallback) successCallback(result);
                } else {
                    this.showNotification(result.message || 'Erro na operação', 'error');
                }
            } else {
                this.showNotification('Erro na requisição', 'error');
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showNotification('Erro na operação', 'error');
        }
    }

    // Função para formatar data
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('pt-BR');
    }

    // Função para formatar hora
    formatTime(timeString) {
        return timeString.substring(0, 5);
    }

    // Função para formatar CPF
    formatCPF(cpf) {
        return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    }

    // Função para formatar telefone
    formatPhone(phone) {
        const cleaned = phone.replace(/\D/g, '');
        if (cleaned.length === 11) {
            return `(${cleaned.substring(0, 2)}) ${cleaned.substring(2, 7)}-${cleaned.substring(7)}`;
        } else if (cleaned.length === 10) {
            return `(${cleaned.substring(0, 2)}) ${cleaned.substring(2, 6)}-${cleaned.substring(6)}`;
        }
        return phone;
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    window.segCondAdmin = new SegCondAdmin();
});

// Funções utilitárias globais
window.SegCondAdmin = {
    // Função para confirmar ações
    confirm: function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    },

    // Função para mostrar loading
    showLoading: function(element) {
        element.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
    },

    // Função para esconder loading
    hideLoading: function(element, content) {
        element.innerHTML = content;
    },

    // Função para debounce
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
};
