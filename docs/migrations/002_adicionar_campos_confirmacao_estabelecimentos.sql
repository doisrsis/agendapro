-- Migration: Adicionar campos de configuração de confirmação e lembretes na tabela estabelecimentos
-- Autor: Rafael Dias - doisr.com.br
-- Data: 03/01/2026

-- Campos de confirmação de agendamento
ALTER TABLE estabelecimentos
ADD COLUMN solicitar_confirmacao TINYINT(1) DEFAULT 1 COMMENT 'Se deve solicitar confirmação do cliente';

ALTER TABLE estabelecimentos
ADD COLUMN confirmacao_horas_antes INT DEFAULT 24 COMMENT 'Quantas horas antes solicitar confirmação';

ALTER TABLE estabelecimentos
ADD COLUMN confirmacao_dia_anterior TINYINT(1) DEFAULT 1 COMMENT 'Se envia no dia anterior';

ALTER TABLE estabelecimentos
ADD COLUMN confirmacao_horario_dia_anterior TIME DEFAULT '18:00:00' COMMENT 'Horário para enviar no dia anterior';

-- Campos de lembrete pré-atendimento
ALTER TABLE estabelecimentos
ADD COLUMN enviar_lembrete_pre_atendimento TINYINT(1) DEFAULT 1 COMMENT 'Se envia lembrete antes do atendimento';

ALTER TABLE estabelecimentos
ADD COLUMN lembrete_minutos_antes INT DEFAULT 60 COMMENT 'Quantos minutos antes enviar lembrete';

ALTER TABLE estabelecimentos
ADD COLUMN lembrete_antecedencia_chegada INT DEFAULT 10 COMMENT 'Minutos de antecedência para pedir ao cliente';

-- Campos de cancelamento automático (opcional)
ALTER TABLE estabelecimentos
ADD COLUMN cancelar_nao_confirmados TINYINT(1) DEFAULT 0 COMMENT 'Se cancela automaticamente não confirmados';

ALTER TABLE estabelecimentos
ADD COLUMN cancelar_nao_confirmados_horas INT DEFAULT 2 COMMENT 'Quantas horas antes cancelar se não confirmar';
