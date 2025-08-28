# 🏢 RBX-Security - Sistema de Vigilância para Condomínios

Sistema completo de vigilância condominial com aplicativo móvel PWA e painel administrativo web.

## 🚀 Visão Geral

O **RBX-Security** é uma solução completa para gerenciar vigilância em condomínios, oferecendo:

- 📱 **Aplicativo Móvel PWA** para vigilantes
- 🖥️ **Painel Administrativo Web** para gestores
- 🗄️ **Banco de Dados MySQL** robusto
- 🔐 **Sistema de Autenticação** seguro
- 📊 **Gestão de Escalas** inteligente
- 🗺️ **Rotas de Vigilância** configuráveis

## ✨ Funcionalidades Principais

### 📱 **App Móvel (Vigilantes)**
- ✅ **Home** com seleção de dia da semana
- ✅ **Postos de Trabalho** atribuídos
- ✅ **Pontos Base** com horários e instruções
- ✅ **Itinerários** entre pontos base
- ✅ **Registro de Presença** nos pontos
- ✅ **Botão de Pânico** para emergências
- ✅ **Sistema de Avisos** para moradores
- ✅ **Notificações** de alertas urgentes
- ✅ **Funcionamento Offline** (PWA)

### 🖥️ **Painel Administrativo**
- ✅ **Dashboard** com estatísticas
- ✅ **Gestão de Pontos Base** (nome, endereço, instruções)
- ✅ **Cartões Programa** (rotas de vigilância)
- ✅ **Cadastro de Vigilantes** (nome, telefone, email)
- ✅ **Sistema de Escalas** semanais
- ✅ **Configuração de Horários** de permanência
- ✅ **Gestão de Itinerários** entre pontos

## 🛠️ Tecnologias Utilizadas

### **Backend**
- **Python 3.8+** - Linguagem principal
- **Flask 2.3.3** - Framework web
- **Flask-SQLAlchemy 3.0.5** - ORM para banco de dados
- **Flask-Login 0.6.3** - Sistema de autenticação
- **MySQL** - Banco de dados principal
- **PyMySQL** - Driver MySQL para Python

### **Frontend**
- **HTML5** - Estrutura das páginas
- **CSS3** - Estilização responsiva
- **JavaScript ES6+** - Funcionalidades interativas
- **Bootstrap 5.3** - Framework CSS
- **Font Awesome 6.4** - Ícones

### **PWA (Progressive Web App)**
- **Service Worker** - Cache offline e sincronização
- **Manifest.json** - Configuração do app
- **Meta tags** - Otimização para mobile
- **Push Notifications** - Notificações em tempo real

## 📋 Pré-requisitos

- **Python 3.8+**
- **MySQL 5.7+**
- **Git** (para clonar o repositório)
- **Navegador moderno** (Chrome, Firefox, Safari, Edge)

## 🚀 Instalação e Configuração

### **1. Clonar o Repositório**
```bash
git clone https://github.com/CelDarley/condominio.git
cd condominio
```

### **2. Configurar Ambiente Python**
```bash
# Criar ambiente virtual
python3 -m venv venv

# Ativar ambiente virtual
source venv/bin/activate  # Linux/Mac
# ou
venv\Scripts\activate     # Windows

# Instalar dependências
pip install -r requirements.txt
```

### **3. Configurar Banco de Dados**
```bash
# Verificar se MySQL está rodando
sudo systemctl status mysql

# Executar script de configuração
mysql -u root -p < database_setup.sql
```

### **4. Configurar Variáveis de Ambiente**
```bash
# Copiar arquivo de exemplo
cp env.example .env

# Editar arquivo .env com suas configurações
nano .env
```

### **5. Executar o Sistema**

#### **Aplicativo Principal (App Móvel)**
```bash
python3 run.py
# Acesse: http://localhost:5001
```

#### **Sistema Administrativo**
```bash
python3 run_admin.py
# Acesse: http://localhost:5002/admin
```

## 🌐 URLs de Acesso

### **Aplicativo Principal**
- **Local**: `http://localhost:5001`
- **Externo**: `http://SEU_IP:5001`

### **Sistema Administrativo**
- **Local**: `http://localhost:5002/admin`
- **Externo**: `http://SEU_IP:5002/admin`

## 🔐 Credenciais Padrão

### **App Móvel (Vigilantes)**
- **Email**: `joao.silva@email.com`
- **Senha**: `123456`

### **Sistema Administrativo**
- **Email**: `admin@segcond.com`
- **Senha**: `admin123`

⚠️ **IMPORTANTE**: Altere as senhas padrão após o primeiro login!

## 📱 Como Usar o App Móvel

### **1. Acessar pelo Smartphone**
- Abra o navegador e acesse a URL do sistema
- Faça login com suas credenciais
- Toque em "Adicionar à Tela Inicial" para instalar como PWA

### **2. Selecionar Dia da Semana**
- Na tela inicial, escolha o dia da semana
- Visualize seus postos de trabalho atribuídos

### **3. Executar Rota de Vigilância**
- Toque no posto de trabalho
- Veja os pontos base e itinerários
- Registre presença em cada ponto base
- Siga as instruções específicas

### **4. Funcionalidades de Emergência**
- **Botão de Pânico**: Para situações urgentes
- **Avisos**: Comunicar com moradores
- **Alertas**: Receber notificações urgentes

## 🖥️ Como Usar o Sistema Administrativo

### **1. Gestão de Pontos Base**
- Cadastrar novos pontos de vigilância
- Definir endereços e instruções
- Configurar horários de permanência

### **2. Criação de Cartões Programa**
- Definir rotas de vigilância
- Estabelecer sequência de pontos base
- Configurar itinerários entre pontos

### **3. Cadastro de Vigilantes**
- Adicionar novos usuários
- Definir permissões e acesso
- Gerenciar informações de contato

### **4. Configuração de Escalas**
- Criar escalas semanais
- Associar vigilantes a postos
- Definir dias de trabalho

## 📊 Estrutura do Banco de Dados

### **Tabelas Principais**
- `usuario` - Usuários do sistema
- `ponto_base` - Pontos de vigilância
- `cartao_programa` - Rotas de vigilância
- `itinerario` - Sequência entre pontos
- `horario_ponto_base` - Horários de permanência
- `escala` - Escalas de trabalho
- `posto_trabalho` - Postos de trabalho
- `aviso` - Avisos para moradores
- `alerta` - Alertas urgentes

## 🔧 Configuração de Rede

### **Acesso Externo**
```bash
# Configurar firewall
sudo ./setup_network.sh

# Verificar acesso
python3 check_access.py
```

### **Porta Externa**
- **App Principal**: 5001
- **Admin**: 5002
- **Configurar port forwarding** no roteador se necessário

## 📈 Monitoramento e Logs

### **Logs do Sistema**
- **Arquivo**: `logs/segcond.log`
- **Nível**: INFO, WARNING, ERROR
- **Rotação**: Automática

### **Métricas Disponíveis**
- Total de usuários ativos
- Pontos base configurados
- Cartões programa ativos
- Escalas em execução

## 🐛 Solução de Problemas

### **Erro de Conexão com Banco**
```bash
# Verificar MySQL
sudo systemctl status mysql

# Verificar credenciais
cat .env | grep DATABASE_URL
```

### **Porta Ocupada**
```bash
# Verificar processos
sudo lsof -i :5001

# Parar processo
sudo pkill -f "python.*app.py"
```

### **Dependências Faltando**
```bash
# Reinstalar
pip install -r requirements.txt --force-reinstall
```

## 🔄 Atualizações e Manutenção

### **Atualizar Sistema**
```bash
git pull origin main
pip install -r requirements.txt
```

### **Backup do Banco**
```bash
mysqldump -u usuario -p segcond_db > backup_$(date +%Y%m%d).sql
```

## 📞 Suporte

### **Canais de Ajuda**
- **Documentação**: Este README
- **Issues**: [GitHub Issues](https://github.com/CelDarley/condominio/issues)
- **Logs**: Arquivos de log do sistema

### **Informações do Projeto**
- **Versão**: 1.0.0
- **Última atualização**: Agosto 2025
- **Licença**: MIT
- **Autor**: CelDarley

## 🤝 Contribuição

Contribuições são bem-vindas! Para contribuir:

1. Faça um fork do projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📄 Licença

Este projeto está licenciado sob a **Licença MIT** - veja o arquivo [LICENSE](LICENSE) para detalhes.

---

## 🎯 **Status do Projeto**

✅ **App Móvel PWA** - Completo e funcional  
✅ **Sistema Administrativo** - Completo e funcional  
✅ **Banco de Dados** - Configurado e otimizado  
✅ **Documentação** - Completa e detalhada  
✅ **Scripts de Instalação** - Automatizados  
✅ **Configuração de Rede** - Para acesso externo  

**🚀 RBX-Security está pronto para uso em produção!**

---

**Desenvolvido com ❤️ para tornar a vigilância condominial mais eficiente e segura.**
