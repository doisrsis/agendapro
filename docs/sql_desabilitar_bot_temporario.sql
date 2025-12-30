-- Desabilitar bot temporariamente enquanto servidor WAHA está offline
-- Rafael Dias - doisr.com.br (29/12/2025)

-- 1. Verificar status atual
SELECT
    id,
    nome,
    waha_status,
    waha_ativo,
    waha_bot_ativo,
    waha_numero_conectado
FROM estabelecimentos
WHERE id = 4;

-- 2. Desabilitar bot e marcar como desconectado
UPDATE estabelecimentos
SET
    waha_status = 'desconectado',
    waha_bot_ativo = 0,
    waha_numero_conectado = NULL
WHERE id = 4;

-- 3. Verificar após atualização
SELECT
    id,
    nome,
    waha_status,
    waha_ativo,
    waha_bot_ativo,
    waha_numero_conectado
FROM estabelecimentos
WHERE id = 4;

-- ============================================
-- APÓS O SERVIDOR WAHA VOLTAR, EXECUTE:
-- ============================================

-- 4. Reabilitar bot
UPDATE estabelecimentos
SET
    waha_bot_ativo = 1,
    waha_ativo = 1
WHERE id = 4;

-- 5. Depois acesse o painel e reconecte o WhatsApp:
-- https://iafila.doisr.com.br/painel/configuracoes?aba=whatsapp
-- Clique em "Conectar WhatsApp" e escaneie o QR Code
