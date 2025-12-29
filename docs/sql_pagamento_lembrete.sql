-- =====================================================
-- SQL: Campos para lembrete de pagamento PIX expirado
-- Autor: Rafael Dias - doisr.com.br
-- Data: 28/12/2024
-- =====================================================

-- 1. Adicionar campo de tempo adicional no estabelecimento
ALTER TABLE `estabelecimentos`
ADD COLUMN IF NOT EXISTS `agendamento_tempo_adicional_pix` INT(11) DEFAULT 5
COMMENT 'Minutos adicionais após expiração do PIX para enviar lembrete';

-- 2. Adicionar campos de controle na tabela de agendamentos
ALTER TABLE `agendamentos`
ADD COLUMN IF NOT EXISTS `pagamento_lembrete_enviado` TINYINT(1) DEFAULT 0
COMMENT 'Flag indicando se lembrete de pagamento foi enviado';

ALTER TABLE `agendamentos`
ADD COLUMN IF NOT EXISTS `pagamento_token` VARCHAR(64) NULL
COMMENT 'Token único para acesso público à página de pagamento';

ALTER TABLE `agendamentos`
ADD COLUMN IF NOT EXISTS `pagamento_expira_adicional_em` DATETIME NULL
COMMENT 'Data/hora de expiração após tempo adicional';

-- 3. Adicionar índice para otimizar busca do cron
ALTER TABLE `agendamentos`
ADD INDEX IF NOT EXISTS `idx_pagamento_pendente` (`pagamento_status`, `pagamento_expira_em`);

-- 4. Adicionar índice para busca por token
ALTER TABLE `agendamentos`
ADD INDEX IF NOT EXISTS `idx_pagamento_token` (`pagamento_token`);

-- 5. Tabela de configurações do cron (opcional - para controle)
CREATE TABLE IF NOT EXISTS `cron_logs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `tipo` VARCHAR(50) NOT NULL COMMENT 'Tipo do cron executado',
    `registros_processados` INT(11) DEFAULT 0,
    `detalhes` TEXT NULL,
    `executado_em` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_tipo_data` (`tipo`, `executado_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Adicionar configuração de token do cron na tabela de configurações
INSERT INTO `configuracoes` (`grupo`, `chave`, `valor`, `descricao`) VALUES
('cron', 'cron_token', 'ALTERAR_ESTE_TOKEN_EM_PRODUCAO', 'Token de segurança para execução do cron')
ON DUPLICATE KEY UPDATE `chave` = `chave`;
