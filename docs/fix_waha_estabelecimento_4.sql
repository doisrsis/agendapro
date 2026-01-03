-- Fix: Configurar WAHA para o estabelecimento ID 4
-- Problema: Campos waha_api_url, waha_api_key vazios causando erro nos cron jobs
-- Data: 03/01/2026

-- Atualizar estabelecimento com configurações WAHA
UPDATE estabelecimentos
SET
    waha_api_url = 'https://zaptotal.doisrsistemas.com.br',
    waha_api_key = 'b781f3e57f4e4c4ba3a67df819050e6e',
    waha_session_name = 'est_4_modelo_barber',
    waha_ativo = 1
WHERE id = 4;

-- Verificar se foi atualizado
SELECT
    id,
    nome,
    waha_api_url,
    waha_api_key,
    waha_session_name,
    waha_ativo
FROM estabelecimentos
WHERE id = 4;
