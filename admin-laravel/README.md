# Sistema de Gestão de Condomínio - Admin Laravel

Sistema completo de gestão de segurança para condomínios com múltiplas aplicações integradas.

## 🏗️ Arquitetura do Sistema

Este projeto faz parte de um sistema distribuído composto por:

- **admin-laravel** - Painel administrativo (este repositório)
- **app-vigilante** - Aplicação mobile para vigilantes  
- **app-morador** - Aplicação mobile para moradores
- **relatorio-camera** - Sistema de relatórios de câmeras

## 🚀 Funcionalidades Principais

### 👑 Administração
- Dashboard administrativo completo
- Gestão de usuários (admins, vigilantes, moradores)
- Controle de postos de trabalho e pontos base
- Criação e gestão de cartões programa
- Sistema de escalas semanais
- Escala diária com ajustes e substituições
- Gestão de moradores e veículos
- Autenticação centralizada

### 🔐 Segurança
- Autenticação robusta com middleware
- Controle de acesso por perfis
- Logs detalhados de atividades
- Validação de dados em todas as operações

### 📱 Integração
- API para aplicações mobile
- Sistema centralizado de usuários
- Sincronização de escalas em tempo real

## 🛠️ Tecnologias Utilizadas

- **Laravel 11** - Framework PHP
- **MySQL** - Banco de dados
- **Bootstrap 5** - Interface responsiva
- **JavaScript** - Interatividade frontend
- **Font Awesome** - Ícones
- **Blade Templates** - Sistema de templates

## ⚙️ Instalação e Configuração

### 1. Pré-requisitos
```bash
- PHP 8.2+
- Composer
- MySQL 5.7+
- Node.js e NPM (opcional)
```

### 2. Clonagem e Dependências
```bash
git clone <repository-url>
cd admin-laravel
composer install
```

### 3. Configuração do Ambiente
```bash
cp .env.example .env
```

Configure as variáveis no `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=segcond_db
DB_USERNAME=segcond
DB_PASSWORD=segcond()123

AUTH_MODEL=App\Models\Usuario
```

### 4. Banco de Dados
```bash
# Executar migrações
php artisan migrate

# Popular banco com dados de exemplo
php artisan db:seed
```

### 5. Iniciar Servidor
```bash
php artisan serve
```

O sistema estará disponível em: `http://localhost:8000`

## 🔑 Credenciais Padrão

### Administrador
- **Email:** admin@segcond.local
- **Senha:** admin123

### Vigilantes de Exemplo
- **Email:** joao@segcond.local, maria@segcond.local, etc.
- **Senha:** 123456

### Moradores de Exemplo
- **Email:** roberto@email.com, fernanda@email.com, etc.
- **Senha:** 123456

## 📊 Estrutura do Banco de Dados

### Tabelas Principais
- `usuario` - Usuários do sistema (admin, vigilantes, moradores)
- `posto_trabalho` - Postos de trabalho para vigilância
- `ponto_base` - Pontos específicos dentro dos postos
- `cartao_programas` - Programas de rondas
- `escala` - Escalas semanais dos vigilantes
- `escala_diaria` - Ajustes diários nas escalas
- `moradores` - Dados dos moradores
- `veiculos` - Veículos dos moradores

## 🎯 Funcionalidades Detalhadas

### Sistema de Escalas
- **Escalas Semanais:** Programação base para cada vigilante
- **Escala Diária:** Ajustes pontuais com substituições
- **Calendário Visual:** Interface intuitiva para gestão
- **Filtros Avançados:** Visualização por vigilante

### Gestão de Postos
- **Postos de Trabalho:** Áreas de responsabilidade
- **Pontos Base:** Locais específicos para ronda
- **Coordenadas GPS:** Localização precisa dos pontos
- **Cartões Programa:** Rotinas predefinidas

### Painel Administrativo
- **Dashboard:** Visão geral do sistema
- **Usuários:** CRUD completo com perfis
- **Moradores:** Gestão de residentes e veículos
- **Relatórios:** Análises e estatísticas

## 🔄 Integração com Outros Apps

### API Endpoints
- `/api/escalas-vigilante/{id}/{ano}/{mes}` - Escalas por vigilante
- `/admin/escala-diaria/calendario` - Dados do calendário
- `/admin/escala-diaria/cartoes-programa` - Cartões por posto

### Autenticação Centralizada
Todas as aplicações utilizam a tabela `usuario` única:
- Tipo: admin, vigilante, morador
- Criptografia segura de senhas
- Controle de status ativo/inativo

## 🧪 Seeders Disponíveis

Execute `php artisan db:seed` para popular:
- ✅ 1 Administrador + 5 Vigilantes
- ✅ 5 Moradores com veículos
- ✅ 3 Postos com pontos base
- ✅ Cartões programa (diurno/noturno)
- ✅ Escalas de exemplo distribuídas

## 🐛 Troubleshooting

### Problemas Comuns

**Erro de migração:** 
```bash
php artisan migrate:fresh --seed
```

**Cache de rotas:**
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

**Permissões de storage:**
```bash
chmod -R 775 storage bootstrap/cache
```

## 📝 Logs e Debug

Logs importantes em:
- `storage/logs/laravel.log` - Logs gerais
- Console do navegador - Debug JavaScript
- Network tab - Requisições AJAX

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto é propriedade privada. Todos os direitos reservados.

## 📞 Suporte

Para suporte técnico ou dúvidas sobre o sistema, entre em contato com a equipe de desenvolvimento.

---

**Desenvolvido com ❤️ para segurança condominial**
