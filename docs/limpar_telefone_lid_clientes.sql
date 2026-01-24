-- ============================================================================
-- Script: Limpar campo telefone de clientes com @lid
-- Autor: Rafael Dias - doisr.com.br
-- Data: 17/01/2026
-- ============================================================================

-- PROBLEMA:
-- Clientes com WhatsApp @lid (ID interno) tiveram o campo telefone preenchido
-- incorretamente com o ID do WhatsApp, que não é um número de telefone real.

-- SOLUÇÃO:
-- Limpar campo telefone para clientes com @lid, deixando NULL.
-- Manter telefone apenas para números reais.

-- ============================================================================
-- 1. LIMPAR TELEFONES DE CLIENTES COM @lid
-- ============================================================================

-- Definir telefone como NULL para clientes com @lid
UPDATE `clientes`
SET `telefone` = NULL
WHERE `whatsapp` LIKE '%@lid';

-- ============================================================================
-- 2. CORRIGIR TELEFONES DE CLIENTES SEM @lid
-- ============================================================================

-- Para clientes sem @lid, garantir que telefone tenha código do país (55)
UPDATE `clientes`
SET `telefone` = CONCAT('55', `telefone`)
WHERE `whatsapp` NOT LIKE '%@lid'
  AND `telefone` IS NOT NULL
  AND LENGTH(`telefone`) <= 11
  AND `telefone` NOT LIKE '55%';

-- ============================================================================
-- 3. VERIFICAR RESULTADO
-- ============================================================================

-- Ver clientes com @lid (devem ter telefone NULL)
SELECT
    id,
    nome,
    whatsapp AS 'WhatsApp',
    telefone AS 'Telefone',
    CASE
        WHEN telefone IS NULL THEN '❌ Sem telefone (esperado para @lid)'
        ELSE '✅ Com telefone'
    END AS 'Status'
FROM clientes
WHERE whatsapp LIKE '%@lid'
ORDER BY id;

-- Ver clientes sem @lid (devem ter telefone válido)
SELECT
    id,
    nome,
    whatsapp AS 'WhatsApp',
    telefone AS 'Telefone',
    CASE
        WHEN LENGTH(telefone) = 13 THEN CONCAT('+', SUBSTR(telefone, 1, 2), ' (', SUBSTR(telefone, 3, 2), ') ', SUBSTR(telefone, 5, 5), '-', SUBSTR(telefone, 10))
        WHEN LENGTH(telefone) = 12 THEN CONCAT('+', SUBSTR(telefone, 1, 2), ' (', SUBSTR(telefone, 3, 2), ') ', SUBSTR(telefone, 5, 4), '-', SUBSTR(telefone, 9))
        ELSE telefone
    END AS 'Telefone Formatado'
FROM clientes
WHERE whatsapp NOT LIKE '%@lid'
ORDER BY id;

-- ============================================================================
-- 4. ESTATÍSTICAS
-- ============================================================================

SELECT
    COUNT(*) AS 'Total de Clientes',
    SUM(CASE WHEN whatsapp LIKE '%@lid' THEN 1 ELSE 0 END) AS 'Com @lid (ID interno)',
    SUM(CASE WHEN whatsapp NOT LIKE '%@lid' THEN 1 ELSE 0 END) AS 'Com número real',
    SUM(CASE WHEN telefone IS NOT NULL THEN 1 ELSE 0 END) AS 'Com telefone preenchido',
    SUM(CASE WHEN telefone IS NULL THEN 1 ELSE 0 END) AS 'Sem telefone'
FROM clientes;

-- ============================================================================
-- OBSERVAÇÕES
-- ============================================================================

/*
NÚMEROS @lid:
- São IDs internos do WhatsApp, não números de telefone reais
- Exemplo: 108259113467972@lid, 166554637451296@lid
- NÃO podem ser usados para ligar ou enviar mensagens via wa.me
- Campo telefone deve ser NULL para estes clientes
- Bot continua funcionando normalmente (usa o ID @lid)

NÚMEROS REAIS:
- Exemplo: 557588890006, (75) 997058104
- Podem ser usados para ligar e enviar mensagens
- Campo telefone deve ter código do país (55)
- Botões WhatsApp funcionam normalmente

RESULTADO ESPERADO:
- Clientes com @lid: telefone = NULL
- Clientes sem @lid: telefone = número com código do país
*/
