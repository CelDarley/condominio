-- Script para configurar o banco de dados SegCond
-- Execute este script como root no MySQL

-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS segcond_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Criar usuário segcond
CREATE USER IF NOT EXISTS 'segcond'@'localhost' IDENTIFIED BY 'segcond()123';

-- Conceder privilégios
GRANT ALL PRIVILEGES ON segcond_db.* TO 'segcond'@'localhost';

-- Aplicar mudanças
FLUSH PRIVILEGES;

-- Selecionar o banco
USE segcond_db;

-- Verificar se foi criado
SELECT 'Banco de dados segcond_db criado com sucesso!' as Status;
SHOW DATABASES LIKE 'segcond_db';
