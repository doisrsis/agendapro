-- SQL para controle de encerramento de sessões do bot
-- Rafael Dias - doisr.com.br (30/12/2025)

-- ============================================
-- VERIFICAÇÃO: Execute primeiro para ver se os campos já existem
-- ============================================
SHOW COLUMNS FROM bot_conversas;

-- ============================================
-- APENAS SE OS CAMPOS NÃO EXISTIREM, execute:
-- ============================================

-- Adicionar campos de controle de sessão (APENAS SE NÃO EXISTIREM)
-- ALTER TABLE bot_conversas
-- ADD COLUMN encerrada TINYINT(1) DEFAULT 0 COMMENT 'Indica se a sessão foi encerrada pelo usuário' AFTER ultima_interacao,
-- ADD COLUMN data_encerramento DATETIME NULL COMMENT 'Data/hora que a sessão foi encerrada' AFTER encerrada;

-- Criar índice para melhorar performance (APENAS SE NÃO EXISTIR)
-- CREATE INDEX idx_encerrada ON bot_conversas(encerrada);

-- ============================================
-- SE OS CAMPOS JÁ EXISTEM, execute isso para garantir valores padrão:
-- ============================================

-- Atualizar registros existentes que não têm o campo encerrada definido
UPDATE bot_conversas
SET encerrada = 0
WHERE encerrada IS NULL;

-- Verificar estrutura final
SHOW COLUMNS FROM bot_conversas;

-- Verificar dados atuais
SELECT id, numero_whatsapp, estado, encerrada, data_encerramento, ultima_interacao
FROM bot_conversas
ORDER BY ultima_interacao DESC;
