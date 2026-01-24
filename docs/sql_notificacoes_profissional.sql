-- ============================================
-- SQL: Adicionar campos de notificações para profissionais
-- Autor: Rafael Dias - doisr.com.br
-- Data: 22/01/2026
-- ============================================

-- Adicionar campos de configuração de notificações na tabela estabelecimentos
ALTER TABLE estabelecimentos
ADD COLUMN IF NOT EXISTS notif_prof_novo_agendamento TINYINT(1) DEFAULT 1 COMMENT 'Notificar profissional sobre novo agendamento',
ADD COLUMN IF NOT EXISTS notif_prof_cancelamento TINYINT(1) DEFAULT 1 COMMENT 'Notificar profissional sobre cancelamento',
ADD COLUMN IF NOT EXISTS notif_prof_reagendamento TINYINT(1) DEFAULT 1 COMMENT 'Notificar profissional sobre reagendamento',
ADD COLUMN IF NOT EXISTS notif_prof_resumo_diario TINYINT(1) DEFAULT 1 COMMENT 'Enviar resumo diário da agenda',
ADD COLUMN IF NOT EXISTS notif_prof_resumo_manha TIME NULL COMMENT 'Horário para envio do resumo da manhã',
ADD COLUMN IF NOT EXISTS notif_prof_resumo_tarde TIME NULL COMMENT 'Horário para envio do resumo da tarde';

-- Definir valores padrão para horários de resumo
UPDATE estabelecimentos
SET notif_prof_resumo_manha = '08:00:00',
    notif_prof_resumo_tarde = '13:00:00'
WHERE notif_prof_resumo_manha IS NULL
   OR notif_prof_resumo_tarde IS NULL;

-- ============================================
-- CONFIGURAÇÃO DO CRON
-- ============================================
--
-- Adicionar no crontab do servidor:
-- */15 * * * * curl -s "https://iafila.doisr.com.br/cron/enviar_resumos_diarios?token=SEU_TOKEN" > /dev/null 2>&1
--
-- Este cron deve rodar a cada 15 minutos para verificar se é hora de enviar os resumos
-- ============================================
