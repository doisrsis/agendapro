-- ============================================================================
-- CORREÇÃO: Foreign Key da tabela logs
-- ============================================================================
-- Problema: logs ainda aponta para usuarios_backup
-- Solução: Recriar foreign key apontando para usuarios
-- ============================================================================

-- Remover foreign key antiga
ALTER TABLE `logs` DROP FOREIGN KEY `fk_logs_usuario`;

-- Adicionar foreign key correta
ALTER TABLE `logs`
ADD CONSTRAINT `fk_logs_usuario`
FOREIGN KEY (`usuario_id`)
REFERENCES `usuarios` (`id`)
ON DELETE SET NULL;

-- Verificar
SELECT
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE
    TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'logs'
    AND REFERENCED_TABLE_NAME IS NOT NULL;

-- ============================================================================
-- FIM DA CORREÇÃO
-- ============================================================================
