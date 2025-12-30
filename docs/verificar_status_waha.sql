-- Verificar status atual do WhatsApp do estabelecimento
SELECT
    id,
    nome,
    waha_status,
    waha_ativo,
    waha_numero_conectado,
    waha_session_name,
    waha_bot_ativo,
    bot_timeout_minutos
FROM estabelecimentos
WHERE id = 4;
