#!/bin/bash

echo "=== DEPLOY DAS MIGRATIONS REORGANIZADAS ==="
echo "Este script irá resetar e recriar todas as migrations"
echo ""

read -p "Tem certeza que deseja continuar? (y/N): " confirm
if [[ $confirm != "y" && $confirm != "Y" ]]; then
    echo "Operação cancelada."
    exit 1
fi

echo "1. Fazendo backup do banco atual..."
DB_NAME=$(grep DB_DATABASE .env | cut -d'=' -f2)
DB_USER=$(grep DB_USERNAME .env | cut -d'=' -f2)
DB_PASS=$(grep DB_PASSWORD .env | cut -d'=' -f2 | tr -d '"')

mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "backup_$(date +%Y%m%d_%H%M%S).sql"

echo "2. Resetando migrations..."
php artisan migrate:reset --force

echo "3. Executando novas migrations..."
php artisan migrate --force

echo "4. Verificando status..."
php artisan migrate:status

echo ""
echo "✅ Deploy das migrations concluído com sucesso!"
echo "📁 Backup salvo em: backup_$(date +%Y%m%d_%H%M%S).sql"
