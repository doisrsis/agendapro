-- ============================================================================
-- Atualizar E-mail do Administrador
-- ============================================================================
--
-- Execute este SQL para atualizar o e-mail do usuário administrador
-- para um e-mail REAL que você tenha acesso.
--
-- Substitua 'seu-email@gmail.com' pelo seu e-mail verdadeiro!
-- ============================================================================

UPDATE `usuarios`
SET `email` = 'seu-email@gmail.com'  -- ALTERE AQUI PARA SEU E-MAIL REAL!
WHERE `id` = 1;

-- Verificar se foi atualizado
SELECT id, nome, email, nivel FROM usuarios WHERE id = 1;
