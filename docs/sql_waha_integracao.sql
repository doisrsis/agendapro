-- ============================================================================
-- SQL para Integração WAHA - WhatsApp HTTP API
-- AgendaPro - Sistema de Agendamentos
--
-- @author Rafael Dias - doisr.com.br
-- @date 28/12/2024
-- ============================================================================

-- ============================================================================
-- TABELA CONFIGURACOES (Super Admin do SaaS)
-- Campos para conexão do número do SaaS (notificações para estabelecimentos)
-- ============================================================================

-- Verificar e adicionar campos WAHA na tabela configuracoes
-- Esses campos são para o Super Admin conectar o número do SaaS

INSERT INTO configuracoes (grupo, chave, valor, descricao) VALUES
('waha', 'waha_api_url', 'https://zaptotal.doisrsistemas.com.br', 'URL da API WAHA'),
('waha', 'waha_api_key', 'b781f3e57f4e4c4ba3a67df819050e6e', 'API Key para autenticação na WAHA'),
('waha', 'waha_session_name', 'saas_admin', 'Nome da sessão WAHA para o SaaS'),
('waha', 'waha_webhook_url', 'https://iafila.doisr.com.br/webhook_waha', 'URL do webhook para receber mensagens'),
('waha', 'waha_status', 'desconectado', 'Status da conexão: desconectado, conectando, conectado'),
('waha', 'waha_numero_conectado', '', 'Número conectado no formato 5511999999999'),
('waha', 'waha_ativo', '0', 'Se a integração WAHA está ativa (0 ou 1)'),
('waha', 'waha_usar_para_estabelecimentos', '1', 'Se os estabelecimentos devem usar a mesma API WAHA do SaaS')
ON DUPLICATE KEY UPDATE descricao = VALUES(descricao);

-- ============================================================================
-- TABELA ESTABELECIMENTOS
-- Campos para cada estabelecimento conectar seu próprio número
-- ============================================================================

-- Adicionar campos WAHA na tabela estabelecimentos (se não existirem)
-- Mantém os campos da Evolution API existentes

ALTER TABLE estabelecimentos
    ADD COLUMN IF NOT EXISTS waha_api_url VARCHAR(255) DEFAULT NULL COMMENT 'URL da API WAHA do estabelecimento',
    ADD COLUMN IF NOT EXISTS waha_api_key VARCHAR(255) DEFAULT NULL COMMENT 'API Key WAHA do estabelecimento',
    ADD COLUMN IF NOT EXISTS waha_session_name VARCHAR(100) DEFAULT NULL COMMENT 'Nome da sessão WAHA',
    ADD COLUMN IF NOT EXISTS waha_webhook_url VARCHAR(255) DEFAULT NULL COMMENT 'URL do webhook WAHA',
    ADD COLUMN IF NOT EXISTS waha_status ENUM('desconectado', 'conectando', 'conectado', 'erro') DEFAULT 'desconectado' COMMENT 'Status da conexão WAHA',
    ADD COLUMN IF NOT EXISTS waha_numero_conectado VARCHAR(20) DEFAULT NULL COMMENT 'Número conectado via WAHA',
    ADD COLUMN IF NOT EXISTS waha_ativo TINYINT(1) DEFAULT 0 COMMENT 'Se WAHA está ativo para este estabelecimento',
    ADD COLUMN IF NOT EXISTS waha_bot_ativo TINYINT(1) DEFAULT 0 COMMENT 'Se o bot de agendamento está ativo',
    ADD COLUMN IF NOT EXISTS whatsapp_api_tipo ENUM('evolution', 'waha', 'nenhum') DEFAULT 'nenhum' COMMENT 'Qual API WhatsApp usar';

-- ============================================================================
-- TABELA DE LOG DE MENSAGENS WHATSAPP (opcional, para histórico)
-- ============================================================================

CREATE TABLE IF NOT EXISTS whatsapp_mensagens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    estabelecimento_id INT UNSIGNED DEFAULT NULL COMMENT 'NULL = mensagem do SaaS admin',
    direcao ENUM('entrada', 'saida') NOT NULL,
    numero_destino VARCHAR(20) NOT NULL COMMENT 'Número no formato 5511999999999',
    tipo_mensagem ENUM('texto', 'imagem', 'audio', 'documento', 'localizacao') DEFAULT 'texto',
    conteudo TEXT NOT NULL,
    message_id VARCHAR(100) DEFAULT NULL COMMENT 'ID da mensagem na API',
    status ENUM('enviado', 'entregue', 'lido', 'erro', 'recebido') DEFAULT 'enviado',
    erro_mensagem TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_estabelecimento (estabelecimento_id),
    INDEX idx_numero (numero_destino),
    INDEX idx_direcao (direcao),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log de mensagens WhatsApp enviadas/recebidas';

-- ============================================================================
-- TABELA DE SESSÕES WAHA (para controle de múltiplas sessões)
-- ============================================================================

CREATE TABLE IF NOT EXISTS waha_sessoes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    estabelecimento_id INT UNSIGNED DEFAULT NULL COMMENT 'NULL = sessão do SaaS admin',
    session_name VARCHAR(100) NOT NULL UNIQUE,
    status ENUM('stopped', 'starting', 'scan_qr', 'working', 'failed') DEFAULT 'stopped',
    numero_conectado VARCHAR(20) DEFAULT NULL,
    push_name VARCHAR(100) DEFAULT NULL COMMENT 'Nome do perfil WhatsApp',
    qr_code_base64 TEXT DEFAULT NULL COMMENT 'QR Code em base64 para exibição',
    ultimo_qr_gerado TIMESTAMP DEFAULT NULL,
    ultima_verificacao TIMESTAMP DEFAULT NULL,
    erro_mensagem TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_estabelecimento (estabelecimento_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Controle de sessões WAHA';

-- ============================================================================
-- ÍNDICES ADICIONAIS PARA PERFORMANCE
-- ============================================================================

-- Índice para busca rápida de estabelecimentos com WAHA ativo
CREATE INDEX IF NOT EXISTS idx_waha_ativo ON estabelecimentos(waha_ativo);
CREATE INDEX IF NOT EXISTS idx_whatsapp_api_tipo ON estabelecimentos(whatsapp_api_tipo);
