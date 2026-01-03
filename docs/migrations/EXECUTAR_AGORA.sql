-- ============================================
-- MIGRATION COMPLETA - SISTEMA DE CONFIRMAÇÃO
-- Autor: Rafael Dias - doisr.com.br
-- Data: 03/01/2026
-- ============================================

-- IMPORTANTE: Execute este arquivo completo no phpMyAdmin
-- Selecione todo o conteúdo e clique em "Executar"

-- ============================================
-- TABELA: agendamentos
-- ============================================

ALTER TABLE agendamentos
ADD COLUMN confirmacao_enviada TINYINT(1) DEFAULT 0 COMMENT 'Flag se pedido de confirmação foi enviado';

ALTER TABLE agendamentos
ADD COLUMN confirmacao_enviada_em DATETIME NULL COMMENT 'Quando o pedido foi enviado';

ALTER TABLE agendamentos
ADD COLUMN confirmado_em DATETIME NULL COMMENT 'Quando o cliente confirmou presença';

ALTER TABLE agendamentos
ADD COLUMN lembrete_enviado TINYINT(1) DEFAULT 0 COMMENT 'Flag se lembrete pré-atendimento foi enviado';

ALTER TABLE agendamentos
ADD COLUMN lembrete_enviado_em DATETIME NULL COMMENT 'Quando o lembrete foi enviado';

-- ============================================
-- TABELA: estabelecimentos
-- ============================================

-- Solicitação de Confirmação
ALTER TABLE estabelecimentos
ADD COLUMN solicitar_confirmacao TINYINT(1) DEFAULT 1 COMMENT 'Se deve solicitar confirmação do cliente';

ALTER TABLE estabelecimentos
ADD COLUMN confirmacao_horas_antes INT DEFAULT 24 COMMENT 'Quantas horas antes solicitar confirmação';

ALTER TABLE estabelecimentos
ADD COLUMN confirmacao_dia_anterior TINYINT(1) DEFAULT 1 COMMENT 'Se deve enviar também no dia anterior';

ALTER TABLE estabelecimentos
ADD COLUMN confirmacao_horario_dia_anterior TIME DEFAULT '18:00:00' COMMENT 'Horário para enviar no dia anterior';

-- Lembrete Pré-Atendimento
ALTER TABLE estabelecimentos
ADD COLUMN enviar_lembrete_pre_atendimento TINYINT(1) DEFAULT 1 COMMENT 'Se deve enviar lembrete antes do atendimento';

ALTER TABLE estabelecimentos
ADD COLUMN lembrete_minutos_antes INT DEFAULT 60 COMMENT 'Quantos minutos antes enviar lembrete';

ALTER TABLE estabelecimentos
ADD COLUMN lembrete_antecedencia_chegada INT DEFAULT 10 COMMENT 'Minutos de antecedência sugeridos na mensagem';

-- Cancelamento Automático
ALTER TABLE estabelecimentos
ADD COLUMN cancelar_nao_confirmados TINYINT(1) DEFAULT 0 COMMENT 'Se deve cancelar automaticamente não confirmados';

ALTER TABLE estabelecimentos
ADD COLUMN cancelar_nao_confirmados_horas INT DEFAULT 2 COMMENT 'Quantas horas antes cancelar se não confirmado';

-- ============================================
-- VERIFICAÇÃO
-- ============================================

-- Verificar colunas adicionadas em agendamentos
SELECT
    'agendamentos' as tabela,
    COLUMN_NAME as coluna,
    COLUMN_TYPE as tipo,
    COLUMN_DEFAULT as padrao,
    COLUMN_COMMENT as comentario
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'agendamentos'
AND COLUMN_NAME IN (
    'confirmacao_enviada',
    'confirmacao_enviada_em',
    'confirmado_em',
    'lembrete_enviado',
    'lembrete_enviado_em'
)
ORDER BY ORDINAL_POSITION;

-- Verificar colunas adicionadas em estabelecimentos
SELECT
    'estabelecimentos' as tabela,
    COLUMN_NAME as coluna,
    COLUMN_TYPE as tipo,
    COLUMN_DEFAULT as padrao,
    COLUMN_COMMENT as comentario
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'estabelecimentos'
AND COLUMN_NAME IN (
    'solicitar_confirmacao',
    'confirmacao_horas_antes',
    'confirmacao_dia_anterior',
    'confirmacao_horario_dia_anterior',
    'enviar_lembrete_pre_atendimento',
    'lembrete_minutos_antes',
    'lembrete_antecedencia_chegada',
    'cancelar_nao_confirmados',
    'cancelar_nao_confirmados_horas'
)
ORDER BY ORDINAL_POSITION;

-- ============================================
-- SUCESSO!
-- ============================================
-- Se você vê as colunas listadas acima, a migration foi executada com sucesso!
-- Agora você pode configurar o sistema no painel: Configurações > Agendamento
