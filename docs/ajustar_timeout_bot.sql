-- ============================================================================
-- AJUSTAR TIMEOUT DO BOT (OPCIONAL)
-- ============================================================================
-- Autor: Rafael Dias - doisr.com.br
-- Data: 16/01/2026
--
-- NOTA: Esta configuração é OPCIONAL, pois o código já foi corrigido para
-- desabilitar timeout em estados críticos (confirmando_agendamento, etc.)
--
-- Use este script apenas se quiser aumentar o timeout para outros estados
-- (menu, seleção de serviço, etc.)
-- ============================================================================

-- Verificar configuração atual
SELECT
    id,
    nome,
    bot_timeout_minutos AS 'Timeout Atual (minutos)',
    CASE
        WHEN bot_timeout_minutos IS NULL THEN '30 (padrão)'
        WHEN bot_timeout_minutos = 30 THEN '30 (padrão)'
        WHEN bot_timeout_minutos = 60 THEN '1 hora'
        WHEN bot_timeout_minutos = 120 THEN '2 horas'
        WHEN bot_timeout_minutos = 180 THEN '3 horas'
        ELSE CONCAT(bot_timeout_minutos, ' minutos')
    END AS 'Descrição'
FROM estabelecimentos
WHERE id = 4;

-- ============================================================================
-- OPÇÃO 1: Manter padrão (30 minutos) - RECOMENDADO
-- ============================================================================
-- Não precisa fazer nada, o código já está corrigido!
-- Estados críticos (confirmando_agendamento) não expiram mais.
-- Estados normais (menu, seleção) expiram após 30 minutos.

-- ============================================================================
-- OPÇÃO 2: Aumentar para 1 hora (60 minutos)
-- ============================================================================
-- Use se quiser dar mais tempo para usuário navegar no menu/seleção
/*
UPDATE estabelecimentos
SET bot_timeout_minutos = 60
WHERE id = 4;
*/

-- ============================================================================
-- OPÇÃO 3: Aumentar para 2 horas (120 minutos)
-- ============================================================================
-- Use se quiser dar ainda mais tempo para navegação
/*
UPDATE estabelecimentos
SET bot_timeout_minutos = 120
WHERE id = 4;
*/

-- ============================================================================
-- OPÇÃO 4: Aumentar para 3 horas (180 minutos)
-- ============================================================================
-- Use apenas se realmente necessário (não recomendado)
/*
UPDATE estabelecimentos
SET bot_timeout_minutos = 180
WHERE id = 4;
*/

-- ============================================================================
-- VERIFICAR RESULTADO
-- ============================================================================
SELECT
    id,
    nome,
    bot_timeout_minutos AS 'Timeout (minutos)',
    CASE
        WHEN bot_timeout_minutos IS NULL THEN '30 minutos (padrão)'
        WHEN bot_timeout_minutos = 30 THEN '30 minutos (padrão)'
        WHEN bot_timeout_minutos = 60 THEN '1 hora'
        WHEN bot_timeout_minutos = 120 THEN '2 horas'
        WHEN bot_timeout_minutos = 180 THEN '3 horas'
        ELSE CONCAT(bot_timeout_minutos, ' minutos')
    END AS 'Descrição'
FROM estabelecimentos
WHERE id = 4;

-- ============================================================================
-- IMPORTANTE: ENTENDA A DIFERENÇA
-- ============================================================================
--
-- Estados SEM timeout (SEMPRE funcionam, independente do tempo):
-- - confirmando_agendamento
-- - confirmando_cancelamento
-- - aguardando_acao_agendamento
--
-- Estados COM timeout (expiram após bot_timeout_minutos):
-- - menu
-- - aguardando_servico
-- - aguardando_profissional
-- - aguardando_data
-- - aguardando_hora
-- - confirmando (novo agendamento)
-- - aguardando_cancelamento
-- - gerenciando_agendamento
-- - reagendando_data
-- - reagendando_hora
-- - confirmando_reagendamento
-- - confirmando_saida
--
-- ============================================================================
-- RECOMENDAÇÃO
-- ============================================================================
--
-- Manter bot_timeout_minutos = 30 (padrão)
--
-- Por quê?
-- - Estados críticos (confirmações) já não expiram mais
-- - 30 minutos é suficiente para navegação normal
-- - Evita conversas travadas por muito tempo
-- - Libera recursos do servidor
--
-- ============================================================================
