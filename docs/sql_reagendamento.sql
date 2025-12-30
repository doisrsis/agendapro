-- SQL para implementar sistema de reagendamento
-- Rafael Dias - doisr.com.br (29/12/2025)

-- 1. Adicionar campo permite_reagendamento na tabela estabelecimentos (se não existir)
SET @dbname = DATABASE();
SET @tablename = "estabelecimentos";
SET @columnname = "permite_reagendamento";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE estabelecimentos ADD COLUMN permite_reagendamento TINYINT(1) DEFAULT 1 COMMENT 'Se permite reagendamento de agendamentos' AFTER limite_reagendamentos"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 2. Adicionar contador de reagendamentos na tabela agendamentos (se não existir)
SET @tablename = "agendamentos";
SET @columnname = "qtd_reagendamentos";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE agendamentos ADD COLUMN qtd_reagendamentos TINYINT DEFAULT 0 COMMENT 'Quantidade de vezes que foi reagendado' AFTER motivo_cancelamento"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 3. Atualizar todos os estabelecimentos para permitir reagendamento (se o campo existir)
UPDATE estabelecimentos
SET permite_reagendamento = 1
WHERE permite_reagendamento IS NULL OR permite_reagendamento = 0;
