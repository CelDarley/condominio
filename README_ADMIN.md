# 🏢 SegCond Admin - Sistema de Administração

Sistema administrativo web para gerenciar todas as funcionalidades do SegCond (Sistema de Vigilância para Condomínios).

## 🚀 Funcionalidades Principais

### 1. **Gestão de Pontos Base**
- ✅ Cadastrar novos pontos base com nome e endereço
- ✅ Editar informações existentes
- ✅ Configurar horários de permanência
- ✅ Definir instruções específicas para vigilantes
- ✅ Categorizar por tipo e prioridade

### 2. **Cartões Programa**
- ✅ Criar rotas de vigilância
- ✅ Definir sequência de pontos base
- ✅ Configurar horários de permanência em cada ponto
- ✅ Estabelecer itinerários entre pontos
- ✅ Gerenciar instruções específicas

### 3. **Cadastro de Segurança**
- ✅ Gerenciar vigilantes (nome, telefone, email)
- ✅ Criar contas de acesso
- ✅ Definir permissões e níveis de acesso
- ✅ Controlar status ativo/inativo

### 4. **Gestão de Escalas**
- ✅ Criar escalas semanais
- ✅ Associar vigilantes a postos de trabalho
- ✅ Definir dias da semana para cada escala
- ✅ Configurar datas de início e fim
- ✅ Visualizar distribuição de trabalho

## 🛠️ Tecnologias Utilizadas

- **Backend**: Flask (Python)
- **Banco de Dados**: MySQL com SQLAlchemy
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Framework CSS**: Bootstrap 5.3
- **Ícones**: Font Awesome 6.4
- **Autenticação**: Flask-Login
- **Segurança**: Werkzeug (hash de senhas)

## 📋 Pré-requisitos

- Python 3.8+
- MySQL 5.7+
- Ambiente virtual Python
- Dependências do `requirements.txt`

## 🚀 Instalação e Configuração

### 1. **Preparar o Ambiente**
```bash
# Ativar ambiente virtual
source venv/bin/activate

# Instalar dependências
pip install -r requirements.txt
```

### 2. **Configurar Banco de Dados**
```bash
# Verificar se o MySQL está rodando
sudo systemctl status mysql

# Executar script de configuração do banco
mysql -u root -p < database_setup.sql
```

### 3. **Configurar Variáveis de Ambiente**
O arquivo `.env` deve conter:
```env
DATABASE_URL=mysql+pymysql://usuario:senha@localhost/segcond_db
SECRET_KEY=sua_chave_secreta_aqui
ADMIN_PORT=5002
ADMIN_DEBUG=True
```

### 4. **Executar o Sistema**
```bash
# Executar script de administração
python3 run_admin.py

# Ou executar diretamente
python3 admin.py
```

## 🌐 Acesso ao Sistema

### **URLs de Acesso:**
- **Local**: `http://localhost:5002/admin`
- **Externo**: `http://SEU_IP:5002/admin`

### **Credenciais Padrão:**
- **Email**: `admin@segcond.com`
- **Senha**: `admin123`

⚠️ **IMPORTANTE**: Altere a senha padrão após o primeiro login!

## 📱 Interface do Sistema

### **Dashboard Principal**
- Visão geral com estatísticas
- Cards informativos
- Ações rápidas
- Resumo das escalas da semana

### **Navegação**
- Menu lateral responsivo
- Dropdowns organizados por categoria
- Breadcrumbs para navegação
- Ícones intuitivos

### **Responsividade**
- Interface adaptável para mobile
- Tabelas responsivas
- Formulários otimizados
- Modais adaptáveis

## 🔧 Funcionalidades Técnicas

### **Validação de Formulários**
- Validação em tempo real
- Feedback visual imediato
- Mensagens de erro contextuais
- Prevenção de envio inválido

### **Sistema de Notificações**
- Toasts informativos
- Diferentes tipos (success, error, warning, info)
- Posicionamento automático
- Auto-dismiss configurável

### **Filtros e Busca**
- Busca em tempo real
- Filtros por status
- Ordenação de tabelas
- Paginação automática

### **Modais Interativos**
- Carregamento via AJAX
- Formulários dinâmicos
- Validação em tempo real
- Feedback visual

## 📊 Estrutura do Banco de Dados

### **Tabelas Principais:**
- `usuario` - Usuários do sistema (admin, vigilantes)
- `ponto_base` - Pontos de vigilância
- `cartao_programa` - Rotas de vigilância
- `itinerario` - Sequência entre pontos
- `horario_ponto_base` - Horários de permanência
- `escala` - Escalas de trabalho
- `posto_trabalho` - Postos de trabalho

### **Relacionamentos:**
- Usuários podem ter múltiplas escalas
- Cartões programa têm múltiplos pontos base
- Itinerários conectam pontos base
- Horários são específicos por cartão e ponto

## 🔐 Segurança

### **Autenticação**
- Login obrigatório para todas as rotas
- Sessões seguras
- Logout automático
- Proteção CSRF

### **Autorização**
- Controle de acesso por tipo de usuário
- Rotas protegidas
- Validação de permissões
- Auditoria de ações

### **Dados**
- Senhas criptografadas (pbkdf2:sha256)
- Validação de entrada
- Sanitização de dados
- Logs de auditoria

## 📱 Integração com App Móvel

### **APIs Disponíveis:**
- `/api/escalas/<usuario_id>/<dia_semana>` - Escalas do usuário
- `/api/pontos-base/<id>` - Detalhes do ponto base
- `/api/cartoes-programa` - Lista de cartões programa

### **Sincronização:**
- Dados em tempo real
- Atualizações automáticas
- Cache inteligente
- Sincronização offline

## 🚀 Comandos Úteis

### **Gerenciar Serviços:**
```bash
# Iniciar sistema admin
python3 run_admin.py

# Parar sistema (Ctrl+C)
# Ou buscar processo
ps aux | grep "python.*admin.py"

# Parar processo
sudo pkill -f "python.*admin.py"
```

### **Verificar Status:**
```bash
# Verificar se está rodando
curl http://localhost:5002/admin

# Verificar porta
sudo lsof -i :5002

# Verificar logs
tail -f logs/segcond.log
```

### **Manutenção:**
```bash
# Backup do banco
mysqldump -u usuario -p segcond_db > backup_$(date +%Y%m%d).sql

# Restaurar backup
mysql -u usuario -p segcond_db < backup_arquivo.sql

# Verificar integridade
mysqlcheck -u usuario -p segcond_db
```

## 🐛 Solução de Problemas

### **Erro de Conexão com Banco:**
```bash
# Verificar se MySQL está rodando
sudo systemctl status mysql

# Verificar credenciais no .env
cat .env | grep DATABASE_URL

# Testar conexão
mysql -u usuario -p -h localhost
```

### **Porta Ocupada:**
```bash
# Verificar qual processo usa a porta
sudo lsof -i :5002

# Parar processo
sudo pkill -f "processo_nome"

# Ou usar porta diferente
export ADMIN_PORT=5003
```

### **Dependências Faltando:**
```bash
# Reinstalar dependências
pip install -r requirements.txt --force-reinstall

# Verificar versões
pip list | grep -E "(Flask|SQLAlchemy|Login)"
```

## 📈 Monitoramento e Logs

### **Logs do Sistema:**
- Arquivo: `logs/segcond.log`
- Nível: INFO, WARNING, ERROR
- Rotação automática
- Timestamp em todas as entradas

### **Métricas Disponíveis:**
- Total de usuários
- Pontos base ativos
- Cartões programa
- Escalas ativas
- Acessos por dia

## 🔄 Atualizações e Manutenção

### **Atualizar Sistema:**
```bash
# Fazer backup
git stash
git pull origin main

# Atualizar dependências
pip install -r requirements.txt

# Reiniciar sistema
python3 run_admin.py
```

### **Manutenção Regular:**
- Backup diário do banco
- Verificação de logs
- Monitoramento de performance
- Atualização de dependências

## 📞 Suporte

### **Canais de Ajuda:**
- Documentação: Este arquivo README
- Logs: `logs/segcond.log`
- Console: Mensagens de erro no terminal
- GitHub: Issues e documentação

### **Informações Úteis:**
- Versão: 1.0.0
- Última atualização: Agosto 2025
- Compatibilidade: Python 3.8+, MySQL 5.7+
- Licença: MIT

---

**🎯 SegCond Admin** - Sistema completo para gerenciar vigilância condominial de forma eficiente e segura!
