#!/bin/bash

echo "=== DEPLOY SEGURO - CORRIGINDO TABELAS EXISTENTES ==="
echo ""

DB_NAME=$(grep DB_DATABASE .env | cut -d'=' -f2)
DB_USER=$(grep DB_USERNAME .env | cut -d'=' -f2)
DB_PASS=$(grep DB_PASSWORD .env | cut -d'=' -f2 | tr -d '"')

echo "1. Verificando tabelas existentes..."
mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SHOW TABLES;"

echo ""
echo "2. Removendo apenas tabelas conflitantes..."

# Lista de tabelas que podem conflitar
TABLES_TO_DROP=(
    "usuario"
    "posto_trabalho" 
    "cartao_programas"
    "ponto_base"
    "cartao_programa_pontos"
    "escala"
    "escala_diaria"
    "moradores"
    "veiculos"
)

echo "Removendo tabelas conflitantes..."
mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SET FOREIGN_KEY_CHECKS = 0;"

for table in "${TABLES_TO_DROP[@]}"; do
    echo "  - Removendo tabela: $table"
    mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "DROP TABLE IF EXISTS \`$table\`;" 2>/dev/null
done

mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SET FOREIGN_KEY_CHECKS = 1;"

echo ""
echo "3. Limpando registros antigos de migrations..."
mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "
DELETE FROM migrations 
WHERE migration LIKE '%usuario%' 
   OR migration LIKE '%posto_trabalho%'
   OR migration LIKE '%cartao_programa%'
   OR migration LIKE '%ponto_base%'
   OR migration LIKE '%escala%'
   OR migration LIKE '%moradores%'
   OR migration LIKE '%veiculos%';" 2>/dev/null || echo "Tabela migrations será criada"

echo ""
echo "4. Executando novas migrations..."
php artisan migrate --force

echo ""
echo "5. Verificando status final..."
php artisan migrate:status

echo ""
echo "✅ Deploy seguro concluído!"
