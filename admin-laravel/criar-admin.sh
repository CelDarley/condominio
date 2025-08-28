#!/bin/bash

# Script para criar usuário administrador no SegCond Laravel Admin
# Autor: SegCond Team
# Data: $(date '+%d/%m/%Y')

echo "╔══════════════════════════════════════════════════════════════╗"
echo "║                    SegCond - Criar Admin                     ║"
echo "║              Script para criar usuário administrador         ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ Erro: Execute este script no diretório admin-laravel"
    echo "   Comando: cd admin-laravel && ./criar-admin.sh"
    exit 1
fi

# Verificar se o PHP está disponível
if ! command -v php &> /dev/null; then
    echo "❌ Erro: PHP não está instalado ou não está no PATH"
    exit 1
fi

echo "Escolha uma opção:"
echo "1) Criar admin com dados interativos"
echo "2) Criar admin padrão (admin@segcond.local / admin123)"
echo "3) Listar usuários admin existentes"
echo "4) Sair"
echo ""

read -p "Digite sua opção (1-4): " opcao

case $opcao in
    1)
        echo ""
        echo "📝 Criando administrador com dados personalizados..."
        php artisan admin:create-user
        ;;
    2)
        echo ""
        echo "📝 Criando administrador padrão..."
        php artisan admin:create-user \
            --name="Administrador SegCond" \
            --email="admin@segcond.local" \
            --password="admin123" \
            --phone="(11) 99999-0000"
        ;;
    3)
        echo ""
        echo "👥 Usuários administradores cadastrados:"
        echo ""
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
        ORDER BY data_criacao DESC;" 2>/dev/null
        
        if [ $? -ne 0 ]; then
            echo "❌ Erro ao conectar com o banco de dados"
            echo "   Verifique se o MySQL está rodando e as credenciais estão corretas"
        fi
        ;;
    4)
        echo "👋 Até logo!"
        exit 0
        ;;
    *)
        echo "❌ Opção inválida. Use 1, 2, 3 ou 4."
        exit 1
        ;;
esac

echo ""
echo "✅ Operação concluída!"
echo ""
echo "📋 Informações úteis:"
echo "   • URL Admin: http://localhost:8000/admin"
echo "   • URL Externa: http://$(hostname -I | awk '{print $1}'):8000/admin"
echo "   • Para criar outro admin: ./criar-admin.sh"
echo "   • Para listar admins: mysql -u segcond -p'segcond()123' segcond_db -e \"SELECT * FROM usuario WHERE tipo='admin';\""
echo "" 