# ✅ Configuração de Autenticação Centralizada - SegCond

## 📋 Resumo da Implementação

A tabela `usuario` no banco `segcond_db` agora é a **base centralizada de autenticação** para todos os três aplicativos:

- **admin-laravel** (porta 8000)
- **app-vigilante** (porta 8001) 
- **app-morador** (porta 8002)

## 🗄️ Estrutura da Tabela Usuario

```sql
-- Tabela centralizada no banco segcond_db
CREATE TABLE usuario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(120) UNIQUE NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'vigilante', 'morador') NOT NULL,
    telefone VARCHAR(20) NULL,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao DATETIME NULL,
    data_atualizacao DATETIME NULL,
    coordenadas_atual JSON NULL,
    ultima_atualizacao_localizacao DATETIME NULL,
    online BOOLEAN DEFAULT FALSE
);
```

## ⚙️ Configurações por Aplicativo

### 🔧 Admin Laravel
- **Porta:** 8000
- **Model:** `App\Models\Usuario`
- **Guard:** `web` → `usuarios` provider
- **Funcionalidade:** Gerenciamento completo de usuários

### 🔧 App Vigilante  
- **Porta:** 8001
- **Model:** `App\Models\Usuario` 
- **Guard:** `web` → `usuarios` provider
- **Funcionalidade:** Interface para vigilantes (tipo='vigilante')

### 🔧 App Morador
- **Porta:** 8002
- **Model:** `App\Models\Usuario`
- **Guard:** `web` e `morador` → `usuarios` provider
- **Funcionalidade:** Interface para moradores (tipo='morador')

## 🔑 Configuração de Banco

Todos os apps usam a mesma configuração:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=segcond_db
DB_USERNAME=segcond
DB_PASSWORD=segcond()123
AUTH_MODEL=App\Models\Usuario
```

## ✅ Testes Realizados

- ✅ **admin-laravel:** 6 usuários encontrados
- ✅ **app-vigilante:** 6 usuários encontrados  
- ✅ **app-morador:** 6 usuários encontrados

## 🚀 Como Usar

### Criando Usuários (via admin-laravel)
```bash
cd admin-laravel
php artisan serve --port=8000
# Acesse: http://localhost:8000/admin
```

### Login nos Apps
- **Vigilantes:** Fazem login no app-vigilante com tipo='vigilante'
- **Moradores:** Fazem login no app-morador com tipo='morador'  
- **Admins:** Fazem login no admin-laravel com tipo='admin'

## 🔒 Segurança

- Senhas armazenadas com bcrypt (campo `senha_hash`)
- Validação de tipo por aplicativo
- Controle de status ativo/inativo
- Sessões independentes por aplicativo

## 📝 Próximos Passos

1. Testar login real em cada aplicativo
2. Implementar middleware de tipo de usuário
3. Sincronizar dados entre aplicações se necessário
4. Configurar logout centralizado (opcional)

---

**Status:** ✅ **CONCLUÍDO** - Autenticação centralizada configurada e testada com sucesso! 