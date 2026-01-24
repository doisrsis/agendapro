-- ============================================================================
-- Script: Corrigir campo telefone dos clientes
-- Autor: Rafael Dias - doisr.com.br
-- Data: 17/01/2026
-- ============================================================================

-- PROBLEMA:
-- Clientes com WhatsApp @lid tiveram o ID salvo no campo telefone
-- Precisamos limpar esses dados incorretos

-- SOLUÇÃO:
-- 1. Limpar telefone de clientes com @lid (deixar NULL)
-- 2. Manter telefone de clientes com números reais
-- 3. Adicionar código do país (55) se necessário

-- ============================================================================
-- 1. LIMPAR TELEFONES INCORRETOS (@lid)
-- ============================================================================

-- Clientes com @lid não têm número real, apenas ID interno do WhatsApp
UPDATE `clientes`
SET `telefone` = NULL
WHERE `whatsapp` LIKE '%@lid';

-- ============================================================================
-- 2. CORRIGIR TELEFONES DE NÚMEROS REAIS
-- ============================================================================

-- Adicionar código do país (55) para números brasileiros sem código
UPDATE `clientes`
SET `telefone` = CONCAT('55', `telefone`)
WHERE `whatsapp` NOT LIKE '%@lid'
  AND `telefone` IS NOT NULL
  AND LENGTH(`telefone`) <= 11
  AND `telefone` NOT LIKE '55%';

-- ============================================================================
-- 3. VERIFICAR RESULTADO
-- ============================================================================

-- Clientes com @lid (devem ter telefone NULL)
SELECT
    id,
    nome,
    whatsapp AS 'WhatsApp (@lid)',
    telefone AS 'Telefone',
    CASE
        WHEN telefone IS NULL THEN '✅ Correto (NULL)'
        ELSE '❌ Incorreto (deveria ser NULL)'
    END AS 'Status'
FROM clientes
WHERE whatsapp LIKE '%@lid'
ORDER BY id;

-- Clientes com número real (devem ter telefone válido)
SELECT
    id,
    nome,
    whatsapp AS 'WhatsApp',
    telefone AS 'Telefone',
    LENGTH(telefone) AS 'Tamanho',
    CASE
        WHEN LENGTH(telefone) = 13 THEN CONCAT('+', SUBSTR(telefone, 1, 2), ' (', SUBSTR(telefone, 3, 2), ') ', SUBSTR(telefone, 5, 5), '-', SUBSTR(telefone, 10))
        WHEN LENGTH(telefone) = 12 THEN CONCAT('+', SUBSTR(telefone, 1, 2), ' (', SUBSTR(telefone, 3, 2), ') ', SUBSTR(telefone, 5, 4), '-', SUBSTR(telefone, 9))
        ELSE telefone
    END AS 'Telefone Formatado'
FROM clientes
WHERE whatsapp NOT LIKE '%@lid'
  AND telefone IS NOT NULL
ORDER BY id;

-- ============================================================================
-- 4. ESTATÍSTICAS
-- ============================================================================

SELECT
    COUNT(*) AS 'Total de Clientes',
    SUM(CASE WHEN whatsapp LIKE '%@lid' THEN 1 ELSE 0 END) AS 'Com @lid',
    SUM(CASE WHEN whatsapp NOT LIKE '%@lid' THEN 1 ELSE 0 END) AS 'Com número real',
    SUM(CASE WHEN telefone IS NOT NULL THEN 1 ELSE 0 END) AS 'Com telefone',
    SUM(CASE WHEN telefone IS NULL THEN 1 ELSE 0 END) AS 'Sem telefone'
FROM clientes;

-- ============================================================================
-- OBSERVAÇÕES
-- ============================================================================

/*
ESTRUTURA CORRETA:

CLIENTE COM @lid:
- whatsapp: "166554637451296@lid" (ID interno do WhatsApp)
- telefone: NULL (não temos número real)
- Botões WhatsApp: Ocultos (alerta exibido)

CLIENTE COM NÚMERO REAL:
- whatsapp: "557588890006" ou "(75) 997058104"
- telefone: "557588890006" ou "5575997058104" (com código do país)
- Botões WhatsApp: Funcionam normalmente

PRÓXIMOS CLIENTES:
- Webhook extrai número real do campo SenderAlt
- Salva automaticamente no campo telefone
- Botões WhatsApp funcionam desde o primeiro contato
*/
