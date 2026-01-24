-- ============================================================================
-- Script: Atualizar telefone da cliente Railda Oliveira
-- Autor: Rafael Dias - doisr.com.br
-- Data: 17/01/2026
-- ============================================================================

-- Cliente: Railda Oliveira
-- WhatsApp: 108259113467972@lid
-- Telefone Real: 557599935560 (extraído do SenderAlt no webhook)

-- ============================================================================
-- ATUALIZAR TELEFONE
-- ============================================================================

UPDATE `clientes`
SET `telefone` = '557599935560'
WHERE `whatsapp` = '108259113467972@lid';

-- ============================================================================
-- VERIFICAR RESULTADO
-- ============================================================================

SELECT
    id,
    nome,
    whatsapp AS 'WhatsApp',
    telefone AS 'Telefone',
    CASE
        WHEN LENGTH(telefone) = 12 THEN CONCAT('+', SUBSTR(telefone, 1, 2), ' (', SUBSTR(telefone, 3, 2), ') ', SUBSTR(telefone, 5, 4), '-', SUBSTR(telefone, 9))
        ELSE telefone
    END AS 'Telefone Formatado'
FROM clientes
WHERE whatsapp = '108259113467972@lid';

-- ============================================================================
-- RESULTADO ESPERADO
-- ============================================================================

/*
id: 10
nome: Railda Oliveira
WhatsApp: 108259113467972@lid
Telefone: 557599935560
Telefone Formatado: +55 (75) 9993-5560

Agora os botões WhatsApp funcionarão corretamente na view!
*/
