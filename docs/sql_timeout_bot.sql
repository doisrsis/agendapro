-- SQL para adicionar timeout configurável do bot
-- Rafael Dias - doisr.com.br (29/12/2025)

-- Adicionar campo bot_timeout_minutos na tabela estabelecimentos
ALTER TABLE estabelecimentos
ADD COLUMN bot_timeout_minutos INT DEFAULT 30
COMMENT 'Tempo em minutos para expirar sessão do bot (padrão: 30)'
AFTER waha_bot_ativo;

-- Atualizar estabelecimentos existentes com valor padrão
UPDATE estabelecimentos
SET bot_timeout_minutos = 30
WHERE bot_timeout_minutos IS NULL;
