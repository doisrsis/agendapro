-- ============================================================================
-- Migration: Sistema de Tentativas Múltiplas de Confirmação
-- Autor: Rafael Dias - doisr.com.br
-- Data: 09/01/2026 22:52
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

-- ============================================================================
-- Notas de Implementação:
-- ============================================================================
--
-- FLUXO:
-- 1. Dia anterior às 19h (ou horário configurado) → 1ª tentativa
-- 2. Aguarda X minutos (configurável) → 2ª tentativa (se não respondeu)
-- 3. Aguarda X minutos (configurável) → 3ª tentativa (se não respondeu)
-- 4. Aguarda X minutos (configurável) → Cancelamento automático (se não respondeu)
--
-- IMPORTANTE:
-- - Todas as tentativas acontecem NO DIA ANTERIOR
-- - Não envia para agendamentos com 2+ dias de antecedência
-- - Intervalo configurável em MINUTOS (não horas)
-- - Após 3ª tentativa sem resposta, cancela automaticamente
--
-- EXEMPLO (intervalo de 30 minutos):
-- Dia anterior 19:00 → 1ª tentativa (confirmacao_tentativas = 1)
-- Dia anterior 19:30 → 2ª tentativa (confirmacao_tentativas = 2)
-- Dia anterior 20:00 → 3ª tentativa (confirmacao_tentativas = 3)
-- Dia anterior 20:30 → Cancelamento automático
--
-- ============================================================================
