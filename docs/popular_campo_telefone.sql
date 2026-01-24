-- ============================================================================
-- Script: Popular campo telefone nos clientes existentes
-- Autor: Rafael Dias - doisr.com.br
-- Data: 17/01/2026
-- ============================================================================

-- IMPORTANTE: Execute este script APÓS adicionar a coluna telefone
-- usando o arquivo: adicionar_campo_telefone_clientes.sql

-- ============================================================================
-- POPULAR CAMPO TELEFONE COM DADOS EXISTENTES
-- ============================================================================

-- Atualizar todos os clientes que ainda não têm telefone preenchido
UPDATE `clientes`
SET `telefone` = REGEXP_REPLACE(`whatsapp`, '[^0-9]', '')
WHERE `telefone` IS NULL OR `telefone` = '';

-- ============================================================================
-- VERIFICAR RESULTADO
-- ============================================================================

-- Ver alguns exemplos de antes e depois
SELECT
    id,
    nome,
    whatsapp AS 'WhatsApp (formato original)',
    telefone AS 'Telefone (apenas dígitos)',
    CASE
        WHEN LENGTH(telefone) = 13 THEN CONCAT('+', SUBSTR(telefone, 1, 2), ' (', SUBSTR(telefone, 3, 2), ') ', SUBSTR(telefone, 5, 5), '-', SUBSTR(telefone, 10))
        WHEN LENGTH(telefone) = 12 THEN CONCAT('+', SUBSTR(telefone, 1, 2), ' (', SUBSTR(telefone, 3, 2), ') ', SUBSTR(telefone, 5, 4), '-', SUBSTR(telefone, 9))
        WHEN LENGTH(telefone) = 11 THEN CONCAT('(', SUBSTR(telefone, 1, 2), ') ', SUBSTR(telefone, 3, 5), '-', SUBSTR(telefone, 8))
        WHEN LENGTH(telefone) = 10 THEN CONCAT('(', SUBSTR(telefone, 1, 2), ') ', SUBSTR(telefone, 3, 4), '-', SUBSTR(telefone, 7))
        ELSE telefone
    END AS 'Telefone Formatado'
FROM clientes
ORDER BY id DESC
LIMIT 20;

-- Contar registros atualizados
SELECT
    COUNT(*) AS 'Total de Clientes',
    SUM(CASE WHEN telefone IS NOT NULL AND telefone != '' THEN 1 ELSE 0 END) AS 'Com Telefone',
    SUM(CASE WHEN telefone IS NULL OR telefone = '' THEN 1 ELSE 0 END) AS 'Sem Telefone'
FROM clientes;
