-- ============================================================================
-- Script: Deletar clientes de teste (IDs 13, 15, 16)
-- Autor: Rafael Dias - doisr.com.br
-- Data: 18/01/2026
-- ============================================================================

-- ATENÇÃO: Este script deleta PERMANENTEMENTE os clientes e todos os dados relacionados
-- Clientes a serem deletados:
--   ID 13: Cliente de teste
--   ID 15: Railda Oliveira (108259113467972@lid)
--   ID 16: Mary Oliveira (557597058104@c.us)

-- ============================================================================
-- VERIFICAR CLIENTES ANTES DE DELETAR
-- ============================================================================

SELECT
    id,
    nome,
    whatsapp,
    telefone,
    origem,
    criado_em
FROM clientes
WHERE id IN (13, 15, 16)
ORDER BY id;

-- ============================================================================
-- DELETAR DADOS RELACIONADOS (ORDEM IMPORTANTE)
-- ============================================================================

-- 1. Deletar conversas do bot
DELETE FROM bot_conversas
WHERE numero_whatsapp IN (
    SELECT whatsapp FROM clientes WHERE id IN (13, 15, 16)
);

-- 2. Deletar agendamentos
DELETE FROM agendamentos
WHERE cliente_id IN (13, 15, 16);

-- 3. Deletar mensagens WhatsApp (se existir a tabela)
DELETE FROM whatsapp_mensagens
WHERE numero_destino IN (
    SELECT whatsapp FROM clientes WHERE id IN (13, 15, 16)
)
OR numero_origem IN (
    SELECT whatsapp FROM clientes WHERE id IN (13, 15, 16)
);

-- 4. Deletar notificações (se existir a tabela)
DELETE FROM notificacoes
WHERE cliente_id IN (13, 15, 16);

-- 5. Deletar avaliações (se existir a tabela)
DELETE FROM avaliacoes
WHERE cliente_id IN (13, 15, 16);

-- 6. Deletar histórico de ações (se existir a tabela)
DELETE FROM cliente_historico
WHERE cliente_id IN (13, 15, 16);

-- ============================================================================
-- DELETAR CLIENTES
-- ============================================================================

DELETE FROM clientes
WHERE id IN (13, 15, 16);

-- ============================================================================
-- VERIFICAR RESULTADO
-- ============================================================================

-- Verificar se clientes foram deletados
SELECT COUNT(*) AS 'Clientes Restantes (deve ser 0)'
FROM clientes
WHERE id IN (13, 15, 16);

-- Verificar agendamentos restantes
SELECT COUNT(*) AS 'Agendamentos Restantes (deve ser 0)'
FROM agendamentos
WHERE cliente_id IN (13, 15, 16);

-- Verificar conversas restantes
SELECT COUNT(*) AS 'Conversas Bot Restantes (deve ser 0)'
FROM bot_conversas
WHERE numero_whatsapp IN ('108259113467972@lid', '557597058104@c.us');

-- ============================================================================
-- RESETAR AUTO_INCREMENT (OPCIONAL)
-- ============================================================================

-- Se quiser resetar o auto_increment da tabela clientes para o próximo ID disponível:
-- ALTER TABLE clientes AUTO_INCREMENT = 1;

-- Ou para um valor específico (exemplo: próximo será 17):
-- ALTER TABLE clientes AUTO_INCREMENT = 17;

-- ============================================================================
-- RESULTADO ESPERADO
-- ============================================================================

/*
Todos os dados dos clientes 13, 15 e 16 foram removidos:
- Clientes deletados: 3
- Agendamentos deletados: todos relacionados
- Conversas bot deletadas: todas relacionadas
- Mensagens WhatsApp deletadas: todas relacionadas

Agora você pode refazer os testes de cadastro via bot!
*/
