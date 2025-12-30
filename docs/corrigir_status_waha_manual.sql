-- Corrigir status WhatsApp manualmente
-- Rafael Dias - doisr.com.br (29/12/2025)

-- Primeiro, verificar o status atual
SELECT
    id,
    nome,
    waha_status,
    waha_ativo,
    waha_numero_conectado,
    waha_session_name
FROM estabelecimentos
WHERE id = 4;

-- Se o WhatsApp está realmente conectado, execute:
UPDATE estabelecimentos
SET
    waha_status = 'conectado',
    waha_ativo = 1,
    waha_bot_ativo = 1
WHERE id = 4;

-- Verificar novamente após atualização
SELECT
    id,
    nome,
    waha_status,
    waha_ativo,
    waha_numero_conectado,
    waha_session_name
FROM estabelecimentos
WHERE id = 4;
