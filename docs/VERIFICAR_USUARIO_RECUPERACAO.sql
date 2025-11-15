-- ============================================================================
-- VERIFICAR USUÁRIO PARA RECUPERAÇÃO DE SENHA
-- ============================================================================
-- Autor: Rafael Dias - doisr.com.br
-- Data: 15/11/2024
-- ============================================================================

USE `cecriativocom_lecortine_orc`;

-- 1. Verificar se o usuário admin existe
SELECT 
    id,
    nome,
    email,
    nivel,
    status,
    token_recuperacao,
    token_expiracao,
    criado_em,
    atualizado_em
FROM `usuarios` 
WHERE `email` = 'admin@lecortine.com.br';

-- 2. Verificar estrutura da tabela (se tem os campos de token)
DESCRIBE `usuarios`;

-- 3. Se os campos não existirem, adicionar:
-- ALTER TABLE `usuarios` 
-- ADD COLUMN `token_recuperacao` VARCHAR(255) NULL AFTER `senha`,
-- ADD COLUMN `token_expiracao` DATETIME NULL AFTER `token_recuperacao`;

-- 4. Limpar tokens expirados (se necessário)
-- UPDATE `usuarios` 
-- SET `token_recuperacao` = NULL, `token_expiracao` = NULL 
-- WHERE `token_expiracao` < NOW();

-- ============================================================================
-- TESTE MANUAL DE RECUPERAÇÃO
-- ============================================================================

-- Para testar manualmente, você pode:
-- 1. Gerar um token manualmente:
-- UPDATE `usuarios` 
-- SET 
--     `token_recuperacao` = 'abc123def456',
--     `token_expiracao` = DATE_ADD(NOW(), INTERVAL 1 HOUR)
-- WHERE `email` = 'admin@lecortine.com.br';

-- 2. Depois acesse:
-- http://localhost/orcamento/auth/resetar_senha/abc123def456

-- ============================================================================
