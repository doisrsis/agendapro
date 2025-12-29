-- =====================================================
-- SQL: Tabela de estados do bot WhatsApp
-- Autor: Rafael Dias - doisr.com.br
-- Data: 29/12/2024
-- =====================================================

-- Tabela para armazenar o estado da conversa do bot
CREATE TABLE IF NOT EXISTS `bot_conversas` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `estabelecimento_id` INT(11) NOT NULL,
    `numero_whatsapp` VARCHAR(20) NOT NULL,
    `estado` VARCHAR(50) NOT NULL DEFAULT 'menu' COMMENT 'Estado atual: menu, aguardando_servico, aguardando_profissional, aguardando_data, aguardando_hora, confirmando',
    `dados_temporarios` TEXT NULL COMMENT 'Dados temporários do fluxo em JSON (servico_id, profissional_id, data, hora)',
    `cliente_id` INT(11) NULL,
    `ultima_interacao` DATETIME NOT NULL,
    `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_estabelecimento_numero` (`estabelecimento_id`, `numero_whatsapp`),
    INDEX `idx_ultima_interacao` (`ultima_interacao`),
    INDEX `idx_estabelecimento` (`estabelecimento_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Limpar conversas antigas (mais de 24 horas sem interação)
-- Executar via cron diariamente
-- DELETE FROM bot_conversas WHERE ultima_interacao < DATE_SUB(NOW(), INTERVAL 24 HOUR);
