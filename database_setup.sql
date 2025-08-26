-- Script de configuração do banco de dados SegCond
-- Execute este script no seu servidor MySQL

-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS segcond_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE segcond_db;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(120) UNIQUE NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    tipo ENUM('vigilante', 'morador', 'admin') DEFAULT 'vigilante',
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de postos de trabalho
CREATE TABLE IF NOT EXISTS posto_trabalho (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de escalas
CREATE TABLE IF NOT EXISTS escala (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    posto_id INT NOT NULL,
    dia_semana TINYINT NOT NULL COMMENT '0=Segunda, 1=Terça, 2=Quarta, 3=Quinta, 4=Sexta, 5=Sábado, 6=Domingo',
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE,
    FOREIGN KEY (posto_id) REFERENCES posto_trabalho(id) ON DELETE CASCADE,
    UNIQUE KEY unique_escala (usuario_id, posto_id, dia_semana)
);

-- Tabela de pontos base
CREATE TABLE IF NOT EXISTS ponto_base (
    id INT AUTO_INCREMENT PRIMARY KEY,
    posto_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    horario_inicio TIME NOT NULL,
    horario_fim TIME NOT NULL,
    tempo_permanencia INT NOT NULL COMMENT 'Tempo em minutos',
    instrucoes TEXT,
    ordem INT DEFAULT 0,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posto_id) REFERENCES posto_trabalho(id) ON DELETE CASCADE
);

-- Tabela de itinerários
CREATE TABLE IF NOT EXISTS itinerario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    posto_id INT NOT NULL,
    ponto_origem_id INT NOT NULL,
    ponto_destino_id INT NOT NULL,
    tempo_estimado INT NOT NULL COMMENT 'Tempo em minutos',
    instrucoes TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posto_id) REFERENCES posto_trabalho(id) ON DELETE CASCADE,
    FOREIGN KEY (ponto_origem_id) REFERENCES ponto_base(id) ON DELETE CASCADE,
    FOREIGN KEY (ponto_destino_id) REFERENCES ponto_base(id) ON DELETE CASCADE
);

-- Tabela de registros de presença
CREATE TABLE IF NOT EXISTS registro_presenca (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    ponto_id INT NOT NULL,
    timestamp_chegada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    timestamp_saida TIMESTAMP NULL,
    observacoes TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE,
    FOREIGN KEY (ponto_id) REFERENCES ponto_base(id) ON DELETE CASCADE
);

-- Tabela de avisos
CREATE TABLE IF NOT EXISTS aviso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    mensagem TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE
);

-- Tabela de alertas
CREATE TABLE IF NOT EXISTS alerta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    morador_id INT NOT NULL,
    vigilante_id INT NOT NULL,
    mensagem TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atendido BOOLEAN DEFAULT FALSE,
    resposta TEXT,
    timestamp_resposta TIMESTAMP NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (morador_id) REFERENCES usuario(id) ON DELETE CASCADE,
    FOREIGN KEY (vigilante_id) REFERENCES usuario(id) ON DELETE CASCADE
);

-- Tabela de logs de sistema
CREATE TABLE IF NOT EXISTS log_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    acao VARCHAR(100) NOT NULL,
    detalhes TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE SET NULL
);

-- Índices para melhor performance
CREATE INDEX idx_usuario_email ON usuario(email);
CREATE INDEX idx_usuario_tipo ON usuario(tipo);
CREATE INDEX idx_escala_dia_semana ON escala(dia_semana);
CREATE INDEX idx_escala_usuario_dia ON escala(usuario_id, dia_semana);
CREATE INDEX idx_ponto_base_posto ON ponto_base(posto_id);
CREATE INDEX idx_ponto_base_ordem ON ponto_base(ordem);
CREATE INDEX idx_itinerario_posto ON itinerario(posto_id);
CREATE INDEX idx_registro_presenca_usuario ON registro_presenca(usuario_id);
CREATE INDEX idx_registro_presenca_data ON registro_presenca(timestamp_chegada);
CREATE INDEX idx_alerta_vigilante ON alerta(vigilante_id);
CREATE INDEX idx_alerta_atendido ON alerta(atendido);
CREATE INDEX idx_log_timestamp ON log_sistema(timestamp);

-- Inserir dados de exemplo
INSERT INTO usuario (nome, email, senha_hash, tipo) VALUES
('Administrador', 'admin@segcond.com', '$2b$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J/HS.iKqG', 'admin'),
('João Silva', 'joao.silva@email.com', '$2b$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J/HS.iKqG', 'vigilante'),
('Maria Santos', 'maria.santos@email.com', '$2b$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J/HS.iKqG', 'vigilante'),
('Carlos Oliveira', 'carlos.oliveira@email.com', '$2b$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4J/HS.iKqG', 'morador');

-- Inserir postos de trabalho
INSERT INTO posto_trabalho (nome, descricao) VALUES
('Portaria Principal', 'Controle de entrada e saída de pessoas e veículos'),
('Ronda Externa', 'Vigilância das áreas externas do condomínio'),
('Ronda Interna', 'Vigilância das áreas internas e corredores'),
('Estacionamento', 'Controle e vigilância do estacionamento');

-- Inserir escalas (exemplo para João Silva - Segunda-feira)
INSERT INTO escala (usuario_id, posto_id, dia_semana) VALUES
(2, 1, 0), -- João Silva na Portaria Principal às segundas
(2, 2, 1), -- João Silva na Ronda Externa às terças
(3, 1, 1), -- Maria Santos na Portaria Principal às terças
(3, 3, 0); -- Maria Santos na Ronda Interna às segundas

-- Inserir pontos base para Portaria Principal
INSERT INTO ponto_base (posto_id, nome, descricao, horario_inicio, horario_fim, tempo_permanencia, instrucoes, ordem) VALUES
(1, 'Recepção', 'Área de recepção e controle de acesso', '08:00:00', '18:00:00', 480, 'Verificar documentos, controlar entrada e saída', 1),
(1, 'Monitoramento', 'Sala de monitoramento de câmeras', '18:00:00', '08:00:00', 840, 'Monitorar câmeras de segurança', 2);

-- Inserir pontos base para Ronda Externa
INSERT INTO ponto_base (posto_id, nome, descricao, horario_inicio, horario_fim, tempo_permanencia, instrucoes, ordem) VALUES
(2, 'Entrada Principal', 'Portão principal de entrada', '08:00:00', '20:00:00', 30, 'Verificar segurança do portão', 1),
(2, 'Área de Lazer', 'Quadras e playground', '20:00:00', '22:00:00', 45, 'Verificar se não há pessoas após o horário', 2),
(2, 'Estacionamento Externo', 'Área de estacionamento externo', '22:00:00', '06:00:00', 60, 'Verificar veículos e segurança', 3);

-- Inserir itinerários
INSERT INTO itinerario (posto_id, ponto_origem_id, ponto_destino_id, tempo_estimado, instrucoes) VALUES
(2, 1, 2, 5, 'Caminhar pela calçada principal'),
(2, 2, 3, 8, 'Atravessar o jardim central'),
(2, 3, 1, 10, 'Retornar pela lateral do condomínio');

-- Inserir alguns avisos de exemplo
INSERT INTO aviso (usuario_id, titulo, mensagem) VALUES
(1, 'Manutenção do Elevador', 'O elevador do bloco A estará em manutenção amanhã das 8h às 12h.'),
(1, 'Limpeza da Piscina', 'A piscina será limpa na próxima segunda-feira. Evitem usar durante a manutenção.');

-- Inserir alguns alertas de exemplo
INSERT INTO alerta (morador_id, vigilante_id, mensagem) VALUES
(4, 2, 'Barulho excessivo no apartamento 101'),
(4, 3, 'Luz queimada no corredor do 2º andar');

-- Criar usuário para a aplicação (ajuste conforme necessário)
-- CREATE USER 'segcond_user'@'localhost' IDENTIFIED BY 'sua_senha_segura';
-- GRANT ALL PRIVILEGES ON segcond_db.* TO 'segcond_user'@'localhost';
-- FLUSH PRIVILEGES;

-- Comentários sobre a estrutura
/*
ESTRUTURA DO BANCO:

1. USUÁRIOS: Vigilantes, moradores e administradores
2. POSTOS DE TRABALHO: Locais onde os vigilantes trabalham
3. ESCALAS: Define qual vigilante trabalha em qual posto em cada dia
4. PONTOS BASE: Locais específicos onde o vigilante deve permanecer
5. ITINERÁRIOS: Rotas entre os pontos base
6. REGISTROS DE PRESENÇA: Controle de chegada/saída nos pontos
7. AVISOS: Comunicações para os moradores
8. ALERTAS: Solicitações urgentes dos moradores
9. LOGS: Histórico de ações do sistema

NOTAS:
- Todas as senhas de exemplo são '123456' (hash bcrypt)
- Ajuste as configurações de usuário e senha conforme necessário
- O banco usa UTF8MB4 para suporte completo a caracteres especiais
- Índices foram criados para otimizar consultas frequentes
*/
