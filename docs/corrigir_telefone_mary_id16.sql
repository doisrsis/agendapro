-- ============================================================================
-- Script: Atualizar telefone da cliente Mary Oliveira (ID 16)
-- Autor: Rafael Dias - doisr.com.br
-- Data: 18/01/2026
-- ============================================================================

-- Cliente: Mary Oliveira (ID 16)
-- WhatsApp: 557597058104@c.us
-- Telefone Incorreto: 53884844269782 (era o @lid do SenderAlt)
-- Telefone Correto: 557597058104 (extraído do from @c.us)
-- Problema: Sistema extraiu SenderAlt ao invés do from para números @c.us

-- ============================================================================
-- ATUALIZAR TELEFONE
-- ============================================================================

UPDATE `clientes`
SET `telefone` = '557597058104'
WHERE `id` = 16
  AND `whatsapp` = '557597058104@c.us'
  AND `telefone` = '53884844269782';

-- ============================================================================
-- VERIFICAR RESULTADO
-- ============================================================================

SELECT
    id,
    nome,
    whatsapp AS 'WhatsApp',
    telefone AS 'Telefone',
    CASE
        WHEN LENGTH(telefone) = 12 THEN CONCAT('+', SUBSTR(telefone, 1, 2), ' (', SUBSTR(telefone, 3, 2), ') ', SUBSTR(telefone, 5, 5), '-', SUBSTR(telefone, 10))
        ELSE telefone
    END AS 'Telefone Formatado',
    criado_em
FROM clientes
WHERE id = 16;

-- ============================================================================
-- RESULTADO ESPERADO
-- ============================================================================

/*
id: 16
nome: Mary Oliveira
WhatsApp: 557597058104@c.us
Telefone: 557597058104
Telefone Formatado: +55 (75) 97058-104
criado_em: 2026-01-18 08:43:40

Agora os botões WhatsApp funcionarão corretamente na view!
*/
