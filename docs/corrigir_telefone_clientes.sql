-- ============================================================================
-- Script: Corrigir campo telefone nos clientes existentes
-- Autor: Rafael Dias - doisr.com.br
-- Data: 17/01/2026
-- ============================================================================

-- PROBLEMA:
-- Números com formato @lid foram salvos sem código do país (55)
-- Exemplo: 108259113467972 (deveria ser 55108259113467972 ou 557599935560)

-- ============================================================================
-- 1. CORRIGIR TELEFONES SEM CÓDIGO DO PAÍS
-- ============================================================================

-- Adicionar código do país (55) para números com 11 dígitos ou menos
UPDATE `clientes`
SET `telefone` = CONCAT('55', `telefone`)
WHERE LENGTH(`telefone`) <= 11
  AND `telefone` NOT LIKE '55%';

-- ============================================================================
-- 2. VERIFICAR RESULTADO
-- ============================================================================

-- Ver todos os clientes com seus números
SELECT
    id,
    nome,
    whatsapp AS 'WhatsApp Original',
    telefone AS 'Telefone (apenas dígitos)',
    LENGTH(telefone) AS 'Tamanho',
    CASE
        WHEN LENGTH(telefone) = 13 THEN CONCAT('+', SUBSTR(telefone, 1, 2), ' (', SUBSTR(telefone, 3, 2), ') ', SUBSTR(telefone, 5, 5), '-', SUBSTR(telefone, 10))
        WHEN LENGTH(telefone) = 12 THEN CONCAT('+', SUBSTR(telefone, 1, 2), ' (', SUBSTR(telefone, 3, 2), ') ', SUBSTR(telefone, 5, 4), '-', SUBSTR(telefone, 9))
        WHEN LENGTH(telefone) = 11 THEN CONCAT('(', SUBSTR(telefone, 1, 2), ') ', SUBSTR(telefone, 3, 5), '-', SUBSTR(telefone, 8))
        WHEN LENGTH(telefone) = 10 THEN CONCAT('(', SUBSTR(telefone, 1, 2), ') ', SUBSTR(telefone, 3, 4), '-', SUBSTR(telefone, 7))
        ELSE telefone
    END AS 'Telefone Formatado'
FROM clientes
ORDER BY id;

-- ============================================================================
-- 3. ESTATÍSTICAS
-- ============================================================================

SELECT
    COUNT(*) AS 'Total de Clientes',
    SUM(CASE WHEN LENGTH(telefone) = 13 THEN 1 ELSE 0 END) AS 'Com 13 dígitos (55 + DDD + 9)',
    SUM(CASE WHEN LENGTH(telefone) = 12 THEN 1 ELSE 0 END) AS 'Com 12 dígitos (55 + DDD + 8)',
    SUM(CASE WHEN LENGTH(telefone) = 11 THEN 1 ELSE 0 END) AS 'Com 11 dígitos (DDD + 9)',
    SUM(CASE WHEN LENGTH(telefone) = 10 THEN 1 ELSE 0 END) AS 'Com 10 dígitos (DDD + 8)',
    SUM(CASE WHEN LENGTH(telefone) < 10 OR LENGTH(telefone) > 13 THEN 1 ELSE 0 END) AS 'Fora do padrão'
FROM clientes;

-- ============================================================================
-- EXEMPLOS DE ANTES E DEPOIS
-- ============================================================================

/*
ANTES DA CORREÇÃO:
- whatsapp: 108259113467972@lid
- telefone: 108259113467972 (15 dígitos - número @lid sem código do país)

DEPOIS DA CORREÇÃO:
- whatsapp: 108259113467972@lid (mantém original)
- telefone: 55108259113467972 (17 dígitos - com código do país)

FORMATAÇÃO:
+55 (10) 82591-13467972 (não é padrão brasileiro)

NOTA: Números @lid do WhatsApp não seguem o padrão brasileiro.
O importante é que o link wa.me funcione corretamente.
*/
