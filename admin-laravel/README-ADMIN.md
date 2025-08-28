# SegCond Admin - Criação de Usuários Administradores

Este documento explica como criar usuários administradores na aplicação Laravel do SegCond.

## 🛠️ Métodos Disponíveis

### 1. Script Interativo (Recomendado)

O método mais fácil é usar o script interativo:

```bash
cd admin-laravel
./criar-admin.sh
```

**Opções disponíveis:**
- `1` - Criar admin com dados personalizados (interativo)
- `2` - Criar admin padrão (admin@segcond.local / admin123)
- `3` - Listar usuários admin existentes
- `4` - Sair

### 2. Comando Artisan Direto

Você também pode usar diretamente o comando Laravel:

```bash
cd admin-laravel

# Modo interativo
php artisan admin:create-user

# Modo direto com parâmetros
php artisan admin:create-user \
    --name="Nome do Admin" \
    --email="admin@exemplo.com" \
    --password="senha123" \
    --phone="(11) 99999-9999"
```

### 3. Parâmetros do Comando

| Parâmetro | Obrigatório | Descrição |
|-----------|-------------|-----------|
| `--name` | Não* | Nome do administrador |
| `--email` | Não* | Email do administrador |
| `--password` | Não* | Senha do administrador |
| `--phone` | Não | Telefone (opcional) |

*Se não fornecido via parâmetro, será solicitado interativamente.

## 📋 Requisitos

- MySQL rodando com usuário `segcond` e senha `segcond()123`
- Banco de dados `segcond_db` configurado
- Laravel configurado com `.env` correto
- PHP 8.2+ disponível

## 🔍 Verificar Usuários Existentes

### Via Script
```bash
./criar-admin.sh
# Escolha opção 3
```

### Via MySQL Direto
```bash
mysql -u segcond -p'segcond()123' segcond_db -e "
SELECT 
    id as 'ID',
    nome as 'Nome', 
    email as 'Email',
    telefone as 'Telefone',
    IF(ativo, 'Ativo', 'Inativo') as 'Status',
    DATE_FORMAT(data_criacao, '%d/%m/%Y %H:%i') as 'Criado em'
FROM usuario 
WHERE tipo = 'admin' 
ORDER BY data_criacao DESC;
"
```

## 🌐 Acessando o Painel Admin

Após criar o usuário admin:

- **URL Local:** http://localhost:8000/admin
- **URL Externa:** http://[IP_DO_SERVIDOR]:8000/admin

Use o email e senha definidos durante a criação para fazer login.

## ⚡ Exemplos Rápidos

### Criar Admin Padrão (Desenvolvimento)
```bash
./criar-admin.sh
# Digite: 2
```

### Criar Admin Personalizado
```bash
php artisan admin:create-user \
    --name="João Silva" \
    --email="joao@condominio.com" \
    --password="minhasenha123" \
    --phone="(11) 98765-4321"
```

### Criar Admin Interativo
```bash
php artisan admin:create-user
# Siga as instruções na tela
```

## 🔐 Estrutura do Usuário Admin

Os usuários admin são criados na tabela `usuario` com:

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | INT | ID único (auto increment) |
| `nome` | VARCHAR(100) | Nome do administrador |
| `email` | VARCHAR(120) | Email (único) |
| `senha_hash` | VARCHAR(255) | Senha criptografada |
| `tipo` | VARCHAR(20) | Sempre 'admin' |
| `ativo` | TINYINT(1) | Sempre TRUE (1) |
| `telefone` | VARCHAR(20) | Telefone (opcional) |
| `data_criacao` | DATETIME | Data/hora de criação |

## 🚨 Solução de Problemas

### Erro de Conexão com Banco
```bash
# Verificar se MySQL está rodando
sudo systemctl status mysql

# Testar conexão
mysql -u segcond -p'segcond()123' segcond_db -e "SELECT 1;"
```

### Email já Existe
```bash
# Listar usuários com email duplicado
mysql -u segcond -p'segcond()123' segcond_db -e "
SELECT email, COUNT(*) as qtd 
FROM usuario 
GROUP BY email 
HAVING COUNT(*) > 1;
"
```

### Permissão Negada no Script
```bash
chmod +x criar-admin.sh
```

## 📝 Logs e Auditoria

Para auditar criação de usuários:

```sql
SELECT 
    nome,
    email,
    tipo,
    data_criacao,
    CASE WHEN ativo THEN 'Ativo' ELSE 'Inativo' END as status
FROM usuario 
WHERE tipo = 'admin'
ORDER BY data_criacao DESC;
```

---

**Desenvolvido pela equipe SegCond** 🏢  
Para suporte, consulte a documentação principal ou entre em contato com o administrador do sistema. 