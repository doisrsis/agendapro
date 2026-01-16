-- ============================================================================
-- AJUSTAR CONFIGURAÇÕES DE CONFIRMAÇÃO E CANCELAMENTO
-- ============================================================================
-- Autor: Rafael Dias - doisr.com.br
-- Data: 16/01/2026
--
-- CENÁRIO:
-- - Cliente agenda com 2h de antecedência mínima
-- - Sistema pede confirmação 2h antes do horário
-- - 3 tentativas com intervalo de 20 minutos
-- - Cancela 1h antes se não confirmar (libera horário para outros)
-- ============================================================================

-- Atualizar estabelecimento ID 4 (modelo barber)
UPDATE estabelecimentos
SET
    -- Tempo mínimo para cliente agendar: 2 horas
    tempo_minimo_agendamento = 120,

    -- Solicitar confirmação: 2 horas antes
    confirmacao_horas_antes = 2,

    -- Intervalo entre tentativas: 20 minutos
    confirmacao_intervalo_tentativas_minutos = 20,

    -- Máximo de tentativas: 3
    confirmacao_max_tentativas = 3,

    -- Cancelar automaticamente: SIM
    confirmacao_cancelar_automatico = 'sim',

    -- Cancelar X horas antes se não confirmar: 1 hora
    -- (Isso libera o horário para outro cliente agendar)
    cancelar_nao_confirmados = 1,
    cancelar_nao_confirmados_horas = 1,

    -- Atualizar timestamp
    atualizado_em = NOW()

WHERE id = 4;

-- ============================================================================
-- VERIFICAR CONFIGURAÇÕES APLICADAS
-- ============================================================================

SELECT
    id,
    nome,
    tempo_minimo_agendamento AS 'Tempo Mínimo (min)',
    confirmacao_horas_antes AS 'Confirmar X horas antes',
    confirmacao_intervalo_tentativas_minutos AS 'Intervalo tentativas (min)',
    confirmacao_max_tentativas AS 'Máx tentativas',
    cancelar_nao_confirmados AS 'Cancelar não confirmados',
    cancelar_nao_confirmados_horas AS 'Cancelar X horas antes',
    confirmacao_cancelar_automatico AS 'Cancelar automático'
FROM estabelecimentos
WHERE id = 4;

-- ============================================================================
-- RESULTADO ESPERADO:
-- ============================================================================
-- Tempo Mínimo (min): 120
-- Confirmar X horas antes: 2
-- Intervalo tentativas (min): 20
-- Máx tentativas: 3
-- Cancelar não confirmados: 1
-- Cancelar X horas antes: 1
-- Cancelar automático: sim
-- ============================================================================
