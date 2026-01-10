-- ============================================================================
-- Migration: Sistema de Tentativas Múltiplas de Confirmação
-- EXECUTAR ESTE ARQUIVO NO PHPMYADMIN
-- ============================================================================

-- Adicionar campos na tabela agendamentos
ALTER TABLE `agendamentos`
ADD COLUMN `confirmacao_tentativas` TINYINT(1) UNSIGNED DEFAULT 0 COMMENT 'Número de tentativas de confirmação enviadas (0-3)',
ADD COLUMN `confirmacao_ultima_tentativa` DATETIME NULL COMMENT 'Data/hora da última tentativa de confirmação';

-- Adicionar campos na tabela estabelecimentos
ALTER TABLE `estabelecimentos`
ADD COLUMN `confirmacao_max_tentativas` TINYINT(1) UNSIGNED DEFAULT 3 COMMENT 'Máximo de tentativas de confirmação (padrão: 3)',
ADD COLUMN `confirmacao_intervalo_tentativas_minutos` SMALLINT(3) UNSIGNED DEFAULT 30 COMMENT 'Intervalo em minutos entre tentativas (padrão: 30min)',
ADD COLUMN `confirmacao_cancelar_automatico` ENUM('sim','nao') DEFAULT 'sim' COMMENT 'Cancelar automaticamente se não confirmar após todas as tentativas';

-- Criar índice para otimizar consultas do cron
CREATE INDEX idx_confirmacao_tentativas ON agendamentos(status, data, confirmacao_tentativas, confirmacao_ultima_tentativa);
