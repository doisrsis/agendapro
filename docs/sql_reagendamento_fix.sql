-- SQL para corrigir sistema de reagendamento
-- Rafael Dias - doisr.com.br (29/12/2025)

-- Verificar se o campo permite_reagendamento existe
SELECT COUNT(*) as campo_existe
FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_schema = DATABASE()
  AND table_name = 'estabelecimentos'
  AND column_name = 'permite_reagendamento';

-- Se o resultado acima for 0, execute este comando:
-- ALTER TABLE estabelecimentos
-- ADD COLUMN permite_reagendamento TINYINT(1) DEFAULT 1
-- COMMENT 'Se permite reagendamento de agendamentos'
-- AFTER limite_reagendamentos;

-- Se o campo já existe, apenas atualize os valores:
UPDATE estabelecimentos
SET permite_reagendamento = 1;

-- Verificar o resultado:
SELECT id, nome, permite_reagendamento, limite_reagendamentos
FROM estabelecimentos;
