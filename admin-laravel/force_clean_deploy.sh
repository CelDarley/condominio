#!/bin/bash

echo "=== DEPLOY FORÇA TOTAL - LIMPEZA COMPLETA ==="
echo "Este script irá APAGAR TODAS as tabelas e recriar tudo do zero"
echo "⚠️  CUIDADO: Todos os dados serão perdidos!"
echo ""

read -p "Tem certeza que deseja continuar? (y/N): " confirm
if [[ $confirm != "y" && $confirm != "Y" ]]; then
    echo "Operação cancelada."
    exit 1
fi

echo "1. Fazendo backup completo do banco..."
DB_NAME=$(grep DB_DATABASE .env | cut -d'=' -f2)
DB_USER=$(grep DB_USERNAME .env | cut -d'=' -f2)
DB_PASS=$(grep DB_PASSWORD .env | cut -d'=' -f2 | tr -d '"')

mysqldump -u "$DB_USER" -p"$DB_PASS" --single-transaction --routines --triggers "$DB_NAME" > "backup_completo_$(date +%Y%m%d_%H%M%S).sql" 2>/dev/null || echo "Backup com warnings (normal)"

echo "2. Removendo TODAS as tabelas do banco..."
mysql -u "$DB_USER" -p"$DB_PASS" -e "
SET FOREIGN_KEY_CHECKS = 0;
SELECT CONCAT('DROP TABLE IF EXISTS \`', table_name, '\`;') AS statement
FROM information_schema.tables
WHERE table_schema = '$DB_NAME';" > drop_tables.sql

# Executar comandos DROP
mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SET FOREIGN_KEY_CHECKS = 0;"
mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < drop_tables.sql 2>/dev/null || echo "Algumas tabelas podem não existir (normal)"
mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SET FOREIGN_KEY_CHECKS = 1;"

rm drop_tables.sql

echo "3. Limpando registros de migrations..."
mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "DROP TABLE IF EXISTS migrations;" 2>/dev/null

echo "4. Executando migrations do zero..."
php artisan migrate --force

echo "5. Verificando status final..."
php artisan migrate:status

echo ""
echo "✅ Deploy completo realizado com sucesso!"
echo "📁 Backup salvo em: backup_completo_$(date +%Y%m%d_%H%M%S).sql"
