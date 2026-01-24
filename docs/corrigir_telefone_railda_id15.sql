-- ============================================================================
-- Script: Atualizar telefone da cliente Railda Oliveira (ID 15)
-- Autor: Rafael Dias - doisr.com.br
-- Data: 18/01/2026
-- ============================================================================

-- Cliente: Railda Oliveira (ID 15)
-- WhatsApp: 108259113467972@lid
-- Telefone Real: 557599935560 (extraído do SenderAlt no webhook)
-- Problema: Cliente criado sem telefone devido a bug no Bot_conversa_model

-- ============================================================================
-- ATUALIZAR TELEFONE
-- ============================================================================

UPDATE `clientes`
SET `telefone` = '557599935560'
WHERE `id` = 15
  AND `whatsapp` = '108259113467972@lid';

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
    END AS 'Telefone Formatado',
    criado_em
FROM clientes
WHERE id = 15;

-- ============================================================================
-- RESULTADO ESPERADO
-- ============================================================================

/*
id: 15
nome: Railda Oliveira
WhatsApp: 108259113467972@lid
Telefone: 557599935560
Telefone Formatado: +55 (75) 9993-5560
criado_em: 2026-01-18 08:26:48

Agora os botões WhatsApp funcionarão corretamente na view!
*/
