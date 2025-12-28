-- SQL para adicionar APENAS campos faltantes de pagamento de agendamentos
-- Data: 27/12/2024
-- Autor: Rafael Dias - doisr.com.br

-- ========================================
-- VERIFICAR E ADICIONAR CAMPOS FALTANTES
-- ========================================

-- TABELA: agendamentos
-- Adicionar campos de pagamento se não existirem

-- Verificar se os campos já existem antes de adicionar
SET @exist_pagamento_id = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'dois8950_agendapro'
    AND TABLE_NAME = 'agendamentos'
    AND COLUMN_NAME = 'pagamento_id');

SET @exist_pagamento_status = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'dois8950_agendapro'
    AND TABLE_NAME = 'agendamentos'
    AND COLUMN_NAME = 'pagamento_status');

-- Se não existir, adicionar campos
SET @sql_agendamentos = IF(@exist_pagamento_id = 0,
    'ALTER TABLE agendamentos
    ADD COLUMN pagamento_id INT NULL AFTER observacoes,
    ADD COLUMN pagamento_status ENUM(''pendente'', ''pago'', ''expirado'', ''cancelado'', ''nao_requerido'') DEFAULT ''nao_requerido'' AFTER pagamento_id,
    ADD COLUMN pagamento_valor DECIMAL(10,2) NULL AFTER pagamento_status,
    ADD COLUMN pagamento_pix_qrcode TEXT NULL AFTER pagamento_valor,
    ADD COLUMN pagamento_pix_copia_cola TEXT NULL AFTER pagamento_pix_qrcode,
    ADD COLUMN pagamento_expira_em DATETIME NULL AFTER pagamento_pix_copia_cola,
    ADD INDEX idx_pagamento_status (pagamento_status),
    ADD INDEX idx_pagamento_expira (pagamento_expira_em);',
    'SELECT ''Campos de pagamento já existem em agendamentos'' AS info;'
);

PREPARE stmt FROM @sql_agendamentos;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========================================
-- TABELA: pagamentos
-- ========================================

-- Verificar se campo agendamento_id existe
SET @exist_agendamento_id = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'dois8950_agendapro'
    AND TABLE_NAME = 'pagamentos'
    AND COLUMN_NAME = 'agendamento_id');

-- Se não existir, adicionar
SET @sql_pagamentos = IF(@exist_agendamento_id = 0,
    'ALTER TABLE pagamentos
    ADD COLUMN agendamento_id INT NULL AFTER assinatura_id,
    ADD INDEX idx_agendamento (agendamento_id);',
    'SELECT ''Campo agendamento_id já existe em pagamentos'' AS info;'
);

PREPARE stmt FROM @sql_pagamentos;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verificar se coluna tipo existe e se tem o valor 'agendamento'
SET @exist_tipo_agendamento = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'dois8950_agendapro'
    AND TABLE_NAME = 'pagamentos'
    AND COLUMN_NAME = 'tipo'
    AND COLUMN_TYPE LIKE '%agendamento%');

-- Se tipo não tiver o valor 'agendamento', modificar
SET @sql_tipo = IF(@exist_tipo_agendamento = 0,
    'ALTER TABLE pagamentos
    MODIFY COLUMN tipo ENUM(''pix'', ''cartao'', ''boleto'', ''assinatura'', ''agendamento'') NOT NULL DEFAULT ''pix'';',
    'SELECT ''Coluna tipo já possui valor agendamento'' AS info;'
);

PREPARE stmt FROM @sql_tipo;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========================================
-- FIM DO SCRIPT
-- ========================================

SELECT 'Script executado com sucesso!' AS resultado;
