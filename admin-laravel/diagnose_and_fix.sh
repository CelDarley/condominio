#!/bin/bash

echo "=== DIAGNÓSTICO E CORREÇÃO DE MIGRATIONS ==="
echo ""

DB_NAME=$(grep DB_DATABASE .env | cut -d'=' -f2)
DB_USER=$(grep DB_USERNAME .env | cut -d'=' -f2)
DB_PASS=$(grep DB_PASSWORD .env | cut -d'=' -f2 | tr -d '"')

echo "📋 INFORMAÇÕES DO BANCO:"
echo "Database: $DB_NAME"
echo "User: $DB_USER"
echo ""

echo "1. 🔍 TABELAS EXISTENTES:"
mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SHOW TABLES;" 2>/dev/null || echo "❌ Erro ao conectar ao banco"

echo ""
echo "2. 📊 STATUS DAS MIGRATIONS:"
php artisan migrate:status

echo ""
echo "3. 🚨 TABELAS CONFLITANTES DETECTADAS:"

CONFLICTING_TABLES=()

# Verificar cada tabela que pode conflitar
EXPECTED_TABLES=("usuario" "posto_trabalho" "cartao_programas" "ponto_base" "cartao_programa_pontos" "escala" "escala_diaria" "moradores" "veiculos")

for table in "${EXPECTED_TABLES[@]}"; do
    result=$(mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SHOW TABLES LIKE '$table';" 2>/dev/null | tail -1)
    if [[ "$result" == "$table" ]]; then
        echo "⚠️  Tabela '$table' já existe"
        CONFLICTING_TABLES+=("$table")
    fi
done

if [[ ${#CONFLICTING_TABLES[@]} -eq 0 ]]; then
    echo "✅ Nenhuma tabela conflitante encontrada"
    echo ""
    echo "🚀 Pode executar: php artisan migrate"
else
    echo ""
    echo "🔧 SOLUÇÕES DISPONÍVEIS:"
    echo ""
    echo "a) SOLUÇÃO RÁPIDA (Recomendada):"
    echo "   ./safe_deploy.sh"
    echo ""
    echo "b) SOLUÇÃO COMPLETA (Remove tudo):"
    echo "   ./force_clean_deploy.sh"
    echo ""
    echo "c) CORREÇÃO MANUAL:"
    echo "   Execute os comandos abaixo:"
    echo ""
    echo "   mysql -u $DB_USER -p'$DB_PASS' $DB_NAME -e \"SET FOREIGN_KEY_CHECKS = 0;\""
    
    for table in "${CONFLICTING_TABLES[@]}"; do
        echo "   mysql -u $DB_USER -p'$DB_PASS' $DB_NAME -e \"DROP TABLE IF EXISTS \\\`$table\\\`;\""
    done
    
    echo "   mysql -u $DB_USER -p'$DB_PASS' $DB_NAME -e \"SET FOREIGN_KEY_CHECKS = 1;\""
    echo "   php artisan migrate"
fi

echo ""
echo "📝 Para mais informações, veja: MIGRATIONS_REORGANIZED.md"
