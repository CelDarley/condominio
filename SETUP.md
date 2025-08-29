# 🚀 Setup Rápido - Sistema de Condomínio

Guia completo para configurar todo o sistema em poucos minutos.

## 📋 Pré-requisitos

```bash
- PHP 8.2+
- Composer
- MySQL 5.7+
- Node.js 16+ (opcional)
- Git
```

## ⚡ Setup em 5 Passos

### 1️⃣ Clonar o Repositório
```bash
git clone https://github.com/CelDarley/condominio.git
cd condominio
```

### 2️⃣ Configurar Banco de Dados
```sql
-- No MySQL
CREATE DATABASE segcond_db;
CREATE USER 'segcond'@'localhost' IDENTIFIED BY 'segcond()123';
GRANT ALL PRIVILEGES ON segcond_db.* TO 'segcond'@'localhost';
FLUSH PRIVILEGES;
```

### 3️⃣ Configurar Admin Laravel
```bash
cd admin-laravel

# Instalar dependências
composer install

# Configurar ambiente
cp .env.example .env

# Editar .env com as configurações do banco
# (DB_DATABASE=segcond_db, DB_USERNAME=segcond, DB_PASSWORD=segcond()123)

# Executar migrações e seeders
php artisan migrate
php artisan db:seed

# Iniciar servidor
php artisan serve
```

### 4️⃣ Configurar App Vigilante
```bash
cd ../app-vigilante

# Instalar dependências
composer install

# Configurar ambiente
cp .env.example .env

# Editar .env com as mesmas configurações do banco

# Iniciar servidor (porta 8001)
php artisan serve --port=8001
```

### 5️⃣ Configurar App Morador
```bash
cd ../app-morador

# Instalar dependências
composer install

# Configurar ambiente
cp .env.example .env

# Editar .env com as mesmas configurações do banco

# Iniciar servidor (porta 8002)
php artisan serve --port=8002
```

## 🌐 URLs do Sistema

Após o setup, acesse:

- **Admin Panel**: http://localhost:8000
- **App Vigilante**: http://localhost:8001  
- **App Morador**: http://localhost:8002

## 🔑 Credenciais Padrão

### Administrador
- **URL**: http://localhost:8000/admin
- **Email**: admin@segcond.local
- **Senha**: admin123

### Vigilantes
- **URL**: http://localhost:8001
- **Email**: joao@segcond.local, maria@segcond.local, pedro@segcond.local
- **Senha**: 123456

### Moradores  
- **URL**: http://localhost:8002
- **Email**: roberto@email.com, fernanda@email.com
- **Senha**: 123456

## 🎯 Testando o Sistema

### 1. Acesso Admin
1. Acesse http://localhost:8000/admin
2. Faça login com admin@segcond.local / admin123
3. Explore: Dashboard → Usuários → Postos → Escalas → Escala Diária

### 2. Acesso Vigilante
1. Acesse http://localhost:8001  
2. Faça login com joao@segcond.local / 123456
3. Veja as escalas do dia e navegue pelos postos

### 3. Acesso Morador
1. Acesse http://localhost:8002
2. Faça login com roberto@email.com / 123456
3. Explore as funcionalidades PWA

## 📊 Dados de Exemplo

O seeder criou automaticamente:
- ✅ 1 Administrador + 5 Vigilantes
- ✅ 5 Moradores com veículos  
- ✅ 3 Postos: Portaria, Ronda Interna, Ronda Externa
- ✅ 9 Pontos base com coordenadas GPS
- ✅ 9 Cartões programa (diurno/noturno/fim de semana)
- ✅ 15 Escalas distribuídas na semana

## 🔧 Comandos Úteis

### Resetar banco completo:
```bash
cd admin-laravel
php artisan migrate:fresh --seed
```

### Limpar cache:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Ver logs:
```bash
tail -f storage/logs/laravel.log
```

## ❗ Problemas Comuns

### Erro de conexão MySQL:
- Verifique se MySQL está rodando
- Confirme credenciais no .env
- Teste: `mysql -u segcond -p'segcond()123' segcond_db`

### Erro de permissões:
```bash
chmod -R 775 storage bootstrap/cache
```

### Erro 500:
- Verifique logs em `storage/logs/laravel.log`
- Confirme se .env está configurado
- Execute `php artisan config:clear`

## 🎉 Pronto!

Sistema completo funcionando com:
- 🏢 **3 aplicações** integradas
- 🔐 **Autenticação centralizada**
- 📅 **Sistema de escalas** inteligente  
- 📱 **Interfaces responsivas**
- 🗃️ **Dados de exemplo** prontos

## 📞 Suporte

Em caso de problemas:
1. Verifique logs do Laravel
2. Confirme configurações do .env
3. Teste conexão com banco
4. Consulte documentação no README.md

---
**Sistema desenvolvido para gestão completa de segurança condominial** 🏠🔒 